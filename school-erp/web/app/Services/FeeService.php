<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\FeeAllocation;
use App\Models\FeeConcession;
use App\Models\FeeReceipt;
use App\Models\FeeReceiptItem;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FeeService
{
    /**
     * Generate sequential and unique Fee Receipt Number.
     * Pattern: SCH/{AY-Short}/REC-{00001}
     */
    public function generateReceiptNumber(?AcademicYear $ay = null): string
    {
        $ay = $ay ?? AcademicYear::current() ?? AcademicYear::first();
        $yearSlug = $ay ? str_replace('20', '', explode('-', $ay->name)[0]) . '-' . str_replace('20', '', explode('-', $ay->name)[1] ?? '27') : '26-27';

        $lastReceipt = FeeReceipt::where('receipt_no', 'like', "SCH/{$yearSlug}/REC-%")
            ->orderByDesc('id')
            ->first();

        $nextSeq = 1;
        if ($lastReceipt && preg_match('/REC-(\d+)$/', $lastReceipt->receipt_no, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        return sprintf("SCH/%s/REC-%05d", $yearSlug, $nextSeq);
    }

    /**
     * Batch allocate a fee structure head to all active students in a class.
     */
    public function allocateFeeStructureToClass(FeeStructure $structure): int
    {
        $students = Student::where('school_class_id', $structure->school_class_id)
            ->where('status', 'active')
            ->get();

        $count = 0;
        DB::transaction(function () use ($students, $structure, &$count) {
            foreach ($students as $student) {
                // Check if concession exists for this student
                $concessionPct = FeeConcession::where('student_id', $student->id)
                    ->where('academic_year_id', $structure->academic_year_id)
                    ->where('concession_type', 'percentage')
                    ->sum('amount');

                $concessionAmount = round(($structure->amount * min(100, $concessionPct)) / 100, 2);
                $netAmount = max(0, $structure->amount - $concessionAmount);

                FeeAllocation::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'fee_structure_id' => $structure->id,
                        'academic_year_id' => $structure->academic_year_id,
                    ],
                    [
                        'amount' => $structure->amount,
                        'concession_amount' => $concessionAmount,
                        'net_amount' => $netAmount,
                        'paid_amount' => 0,
                        'balance_due' => $netAmount,
                        'status' => $netAmount == 0 ? 'paid' : 'pending',
                        'due_date' => $structure->due_date,
                    ]
                );
                $count++;
            }
        });

        return $count;
    }

    /**
     * Retrieve complete financial dues dossier for a student.
     */
    public function getStudentFeeLedger(Student $student, ?AcademicYear $ay = null): array
    {
        $ay = $ay ?? AcademicYear::current() ?? AcademicYear::first();

        $allocations = FeeAllocation::with('feeStructure')
            ->where('student_id', $student->id)
            ->when($ay, fn($q) => $q->where('academic_year_id', $ay->id))
            ->orderBy('due_date')
            ->get();

        $receipts = FeeReceipt::with('collector')
            ->where('student_id', $student->id)
            ->when($ay, fn($q) => $q->where('academic_year_id', $ay->id))
            ->orderByDesc('receipt_date')
            ->get();

        $concessions = FeeConcession::with('approver')
            ->where('student_id', $student->id)
            ->when($ay, fn($q) => $q->where('academic_year_id', $ay->id))
            ->get();

        $totalPayable = $allocations->sum('amount');
        $totalConcession = $allocations->sum('concession_amount');
        $netPayable = $allocations->sum('net_amount');
        $totalPaid = $allocations->sum('paid_amount');
        $balanceDue = $allocations->sum('balance_due');

        return [
            'student' => $student,
            'academic_year' => $ay,
            'allocations' => $allocations,
            'receipts' => $receipts,
            'concessions' => $concessions,
            'total_payable' => $totalPayable,
            'total_concession' => $totalConcession,
            'net_payable' => $netPayable,
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue,
        ];
    }

    /**
     * Cashier collection desk: process student fee payment inside DB transaction.
     */
    public function collectFee(array $data, User $cashier): FeeReceipt
    {
        $student = Student::findOrFail($data['student_id']);
        $amountToPay = (float) $data['amount_paid'];
        $ay = AcademicYear::find($data['academic_year_id'] ?? null) ?? AcademicYear::current() ?? AcademicYear::first();

        if ($amountToPay <= 0) {
            throw new InvalidArgumentException('Collection amount must be greater than zero.');
        }

        return DB::transaction(function () use ($student, $amountToPay, $data, $cashier, $ay) {
            $receiptNo = $this->generateReceiptNumber($ay);

            $receipt = FeeReceipt::create([
                'receipt_no' => $receiptNo,
                'student_id' => $student->id,
                'academic_year_id' => $ay->id,
                'amount_paid' => $amountToPay,
                'concession_applied' => (float) ($data['concession_applied'] ?? 0),
                'payment_method' => $data['payment_method'] ?? 'Cash',
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'receipt_date' => $data['receipt_date'] ?? now()->toDateString(),
                'collected_by' => $cashier->id,
                'notes' => $data['notes'] ?? 'Class tuition & term fees collection',
            ]);

            // Allocate collected amount across pending/partial dues
            $remainingPayment = $amountToPay;
            $pendingAllocations = FeeAllocation::where('student_id', $student->id)
                ->where('academic_year_id', $ay->id)
                ->where('balance_due', '>', 0)
                ->orderBy('due_date')
                ->get();

            foreach ($pendingAllocations as $alloc) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $appliedToAlloc = min($remainingPayment, (float) $alloc->balance_due);
                $alloc->paid_amount += $appliedToAlloc;
                $alloc->balance_due -= $appliedToAlloc;
                $alloc->status = $alloc->balance_due <= 0.01 ? 'paid' : 'partial';
                $alloc->save();

                FeeReceiptItem::create([
                    'fee_receipt_id' => $receipt->id,
                    'fee_allocation_id' => $alloc->id,
                    'description' => ($alloc->feeStructure?->name ?? 'Fee') . ' (' . ($alloc->feeStructure?->term ?? 'Term') . ')',
                    'amount' => $appliedToAlloc,
                ]);

                $remainingPayment -= $appliedToAlloc;
            }

            // If there's surplus payment or no specific allocation yet, record as advance/general item
            if ($remainingPayment > 0) {
                FeeReceiptItem::create([
                    'fee_receipt_id' => $receipt->id,
                    'fee_allocation_id' => null,
                    'description' => 'Tuition / Advance Term Fee Account Credit',
                    'amount' => $remainingPayment,
                ]);
            }

            AuditLog::record('fee_receipt_issued', $receipt, null, [
                'receipt_no' => $receipt->receipt_no,
                'student_id' => $student->id,
                'amount' => $receipt->amount_paid,
                'method' => $receipt->payment_method,
            ]);

            return $receipt;
        });
    }

    /**
     * Apply student concession or scholarship.
     */
    public function applyConcession(array $data, User $approver): FeeConcession
    {
        return DB::transaction(function () use ($data, $approver) {
            $student = Student::findOrFail($data['student_id']);
            $ay = AcademicYear::find($data['academic_year_id'] ?? null) ?? AcademicYear::current() ?? AcademicYear::first();

            $concession = FeeConcession::create([
                'student_id' => $student->id,
                'academic_year_id' => $ay->id,
                'title' => $data['title'],
                'concession_type' => $data['concession_type'] ?? 'percentage',
                'amount' => (float) $data['amount'],
                'reason' => $data['reason'] ?? 'Approved scholarship / fee waiver',
                'approved_by' => $approver->id,
            ]);

            // Rebalance pending allocations for this student
            if ($concession->concession_type === 'percentage') {
                $pct = min(100, (float) $concession->amount);
                $allocations = FeeAllocation::where('student_id', $student->id)
                    ->where('academic_year_id', $ay->id)
                    ->get();

                foreach ($allocations as $alloc) {
                    $newConcession = round(($alloc->amount * $pct) / 100, 2);
                    $alloc->concession_amount = $newConcession;
                    $alloc->net_amount = max(0, $alloc->amount - $newConcession);
                    $alloc->balance_due = max(0, $alloc->net_amount - $alloc->paid_amount);
                    $alloc->status = $alloc->balance_due <= 0.01 ? 'paid' : ($alloc->paid_amount > 0 ? 'partial' : 'pending');
                    $alloc->save();
                }
            }

            AuditLog::record('fee_concession_granted', $concession, null, [
                'student_id' => $student->id,
                'title' => $concession->title,
                'amount' => $concession->amount,
            ]);

            return $concession;
        });
    }

    /**
     * Generate official DomPDF Fee Receipt.
     */
    public function generateReceiptPdf(FeeReceipt $receipt)
    {
        $receipt->load(['student.schoolClass', 'student.section', 'collector', 'items']);
        $ledger = $this->getStudentFeeLedger($receipt->student, $receipt->academicYear);

        $pdf = Pdf::loadView('accounts.fees.receipt_pdf', compact('receipt', 'ledger'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf;
    }
}
