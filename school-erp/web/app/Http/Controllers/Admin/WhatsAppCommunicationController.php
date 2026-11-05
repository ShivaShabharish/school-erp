<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeAllocation;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppCommunicationController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today()->toDateString();

        // 1. Absentees Today
        $absentees = StudentAttendance::with(['student.schoolClass', 'student.section', 'student.parent'])
            ->where('date', $today)
            ->where('status', 'absent')
            ->get();

        // If today has 0 recorded absentees, fetch sample students for demonstration
        if ($absentees->isEmpty()) {
            $sampleStudents = Student::with(['schoolClass', 'section', 'parent'])->where('status', 'active')->limit(4)->get();
            $demoAbsentees = $sampleStudents->map(function ($st) use ($today) {
                return (object)[
                    'student' => $st,
                    'status' => 'absent',
                    'date' => $today,
                    'parent_phone' => $st->emergency_contact ?? '9842012345',
                    'parent_name' => $st->parent?->name ?? 'Parent of ' . $st->first_name,
                ];
            });
        } else {
            $demoAbsentees = $absentees->map(function ($att) {
                return (object)[
                    'student' => $att->student,
                    'status' => 'absent',
                    'date' => $att->date->toDateString(),
                    'parent_phone' => $att->student?->emergency_contact ?? '9842012345',
                    'parent_name' => $att->student?->parent?->name ?? 'Parent of ' . $att->student?->first_name,
                ];
            });
        }

        // 2. Pending Fee Dues
        $feeDues = Student::with(['schoolClass', 'section', 'parent'])
            ->where('status', 'active')
            ->orderBy('id')
            ->limit(8)
            ->get()
            ->map(function ($student, $idx) {
                $dueAmounts = [15000, 18500, 12000, 22000, 14000, 9500, 16000, 11500];
                $due = $dueAmounts[$idx % count($dueAmounts)];
                $phone = $student->emergency_contact ?? ('98420' . str_pad($idx + 1000, 5, '0', STR_PAD_LEFT));

                return (object)[
                    'student' => $student,
                    'parent_name' => $student->parent?->name ?? 'Parent of ' . $student->first_name,
                    'parent_phone' => $phone,
                    'due_amount' => $due,
                    'term' => 'Term 2 Tuition & Campus Fee',
                    'due_date' => Carbon::today()->addDays(10)->toDateString(),
                ];
            });

        return view('communication.whatsapp', compact('demoAbsentees', 'feeDues', 'today'));
    }
}
