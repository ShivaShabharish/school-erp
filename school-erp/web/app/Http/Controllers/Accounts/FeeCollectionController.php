<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeReceipt;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeCollectionController extends Controller
{
    public function __construct(
        protected FeeService $feeService
    ) {}

    public function collect(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        $selectedStudent = null;
        $ledger = null;

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with(['schoolClass', 'section'])->find($request->get('student_id'));
        } elseif ($request->filled('search')) {
            $query = trim($request->get('search'));
            $selectedStudent = Student::with(['schoolClass', 'section'])
                ->where('admission_no', $query)
                ->orWhere('roll_number', $query)
                ->orWhere(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->first();
        }

        if ($selectedStudent) {
            $ledger = $this->feeService->getStudentFeeLedger($selectedStudent, $currentYear);
        }

        $recentReceipts = FeeReceipt::with(['student.schoolClass', 'student.section', 'collector'])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        return view('accounts.fees.collect', compact(
            'classes',
            'selectedStudent',
            'ledger',
            'currentYear',
            'recentReceipts'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount_paid' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:Cash,UPI,Cheque,Bank Transfer,Card,Demand Draft'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'receipt_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $receipt = $this->feeService->collectFee($validated, auth()->user());

        return redirect()->route('accounts.fees.collect', ['student_id' => $receipt->student_id])
            ->with('success', "Fee Receipt {$receipt->receipt_no} generated successfully for ₹ " . number_format($receipt->amount_paid, 2))
            ->with('receipt_id', $receipt->id);
    }

    public function downloadReceipt(FeeReceipt $receipt)
    {
        $pdf = $this->feeService->generateReceiptPdf($receipt);
        $safeName = str_replace(['/', '\\'], '-', $receipt->receipt_no);
        $filename = "Fee-Receipt-{$safeName}.pdf";

        return $pdf->download($filename);
    }

    public function studentDuesJson(Student $student): JsonResponse
    {
        $ledger = $this->feeService->getStudentFeeLedger($student);

        return response()->json([
            'status' => 'success',
            'student_id' => $student->id,
            'name' => $student->full_name,
            'admission_no' => $student->admission_no,
            'class' => $student->schoolClass?->name,
            'section' => $student->section?->name,
            'balance_due' => $ledger['balance_due'],
            'total_paid' => $ledger['total_paid'],
            'net_payable' => $ledger['net_payable'],
        ]);
    }
}
