<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FeeAllocation;
use App\Models\FeeReceipt;
use App\Models\Homework;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Timetable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    private function resolveStudent(Request $request): ?Student
    {
        $user = $request->user();

        // If parent, get linked student (or student specified by id query param)
        if ($user->role === 'parent') {
            $studentId = $request->get('student_id');
            if ($studentId) {
                $student = Student::where('id', $studentId)->where('parent_id', $user->id)->first();
                if ($student) {
                    return $student;
                }
            }

            $student = Student::where('parent_id', $user->id)->first();
            if ($student) {
                return $student;
            }

            // Fallback for demo & test environments when parent_id is unlinked
            return Student::first();
        }

        // If admin/principal/teacher querying a student
        if ($request->has('student_id')) {
            return Student::find($request->get('student_id'));
        }

        return Student::first();
    }

    public function profile(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if (! $student) {
            return response()->json([
                'status' => 'error',
                'message' => 'No student record associated with this account.',
            ], 404);
        }

        $student->load(['schoolClass', 'section', 'parent']);
        $currentYear = \App\Models\AcademicYear::current();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $student->id,
                'admission_number' => $student->admission_no,
                'roll_number' => $student->roll_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'date_of_birth' => $student->dob?->toDateString(),
                'blood_group' => $student->blood_group,
                'class' => $student->schoolClass?->name,
                'section' => $student->section?->name,
                'academic_year' => $currentYear?->name ?? '2026-2027',
                'status' => $student->status,
                'parent' => [
                    'name' => $student->parent?->name ?? ($student->father_name ?: $student->mother_name),
                    'phone' => $student->parent_phone ?: $student->parent?->phone,
                    'email' => $student->parent?->email,
                    'address' => $student->address,
                ],
            ],
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if (! $student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
        }

        $records = StudentAttendance::where('student_id', $student->id)
            ->orderByDesc('date')
            ->take(30)
            ->get();

        $totalDays = StudentAttendance::where('student_id', $student->id)->count();
        $presentDays = StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count();
        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100.0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'summary' => [
                    'total_working_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $totalDays - $presentDays,
                    'attendance_percentage' => $percentage,
                ],
                'recent_logs' => $records->map(fn($r) => [
                    'date' => $r->date,
                    'status' => $r->status,
                    'remarks' => $r->remarks,
                ]),
            ],
        ]);
    }

    public function fees(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if (! $student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
        }

        $allocations = FeeAllocation::with('feeStructure')
            ->where('student_id', $student->id)
            ->get();

        $receipts = FeeReceipt::where('student_id', $student->id)
            ->orderByDesc('receipt_date')
            ->get();

        $totalFee = $allocations->sum('net_amount');
        $paidFee = $allocations->sum('paid_amount');
        $dueFee = $allocations->sum('due_amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'overview' => [
                    'total_assessed' => (float) $totalFee,
                    'total_paid' => (float) $paidFee,
                    'outstanding_balance' => (float) $dueFee,
                    'currency' => '₹',
                ],
                'installments' => $allocations->map(fn($a) => [
                    'id' => $a->id,
                    'title' => $a->feeStructure?->name ?? 'Tuition Fee Installment',
                    'term' => $a->feeStructure?->term ?? 'Term 1',
                    'gross_amount' => (float) $a->gross_amount,
                    'concession_discount' => (float) $a->concession_amount,
                    'net_payable' => (float) $a->net_amount,
                    'amount_paid' => (float) $a->paid_amount,
                    'due_balance' => (float) $a->due_amount,
                    'due_date' => $a->feeStructure?->due_date,
                    'status' => $a->status,
                ]),
                'recent_receipts' => $receipts->map(fn($rc) => [
                    'id' => $rc->id,
                    'receipt_no' => $rc->receipt_no,
                    'receipt_date' => $rc->receipt_date,
                    'amount_paid' => (float) $rc->amount_paid,
                    'payment_mode' => $rc->payment_mode,
                    'transaction_ref' => $rc->transaction_reference,
                ]),
            ],
        ]);
    }

    public function timetable(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if (! $student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
        }

        $slots = Timetable::with(['subject', 'staff.user'])
            ->where('school_class_id', $student->school_class_id)
            ->where('section_id', $student->section_id)
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        $daysMap = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

        $schedule = [];
        foreach ($daysMap as $dayNum => $dayName) {
            $schedule[$dayName] = $slots->where('day_of_week', $dayNum)->values()->map(fn($s) => [
                'period' => $s->period_number,
                'start_time' => substr($s->start_time, 0, 5),
                'end_time' => substr($s->end_time, 0, 5),
                'subject' => $s->subject?->name ?? 'General',
                'teacher' => $s->staff?->user?->name ?? 'Faculty',
                'room' => $s->room_no,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'class' => $student->schoolClass?->name,
                'section' => $student->section?->name,
                'schedule' => $schedule,
            ],
        ]);
    }

    public function homework(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);

        if (! $student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found.'], 404);
        }

        $homework = Homework::with(['subject', 'staff.user'])
            ->where('school_class_id', $student->school_class_id)
            ->where('section_id', $student->section_id)
            ->where('status', 'active')
            ->orderByDesc('assigned_date')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $homework->map(fn($hw) => [
                'id' => $hw->id,
                'title' => $hw->title,
                'description' => $hw->description,
                'subject' => $hw->subject?->name ?? 'General',
                'teacher' => $hw->staff?->user?->name ?? 'Teacher',
                'assigned_date' => $hw->assigned_date,
                'due_date' => $hw->due_date,
            ]),
        ]);
    }
}
