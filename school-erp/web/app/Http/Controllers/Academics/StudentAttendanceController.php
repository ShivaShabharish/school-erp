<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        $selectedClassId = $request->get('class_id', $classes->first()->id ?? null);
        $selectedSectionId = $request->get('section_id', $classes->first()?->sections->first()?->id ?? null);
        $date = $request->get('date', Carbon::today()->toDateString());

        $students = collect();
        $attendances = collect();

        if ($selectedClassId && $selectedSectionId) {
            $students = Student::where('school_class_id', $selectedClassId)
                ->where('section_id', $selectedSectionId)
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $attendances = StudentAttendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $turnoutPct = $students->count() > 0 ? round(($presentCount / $students->count()) * 100, 1) : 0;

        return view('academics.attendance.index', compact(
            'classes',
            'selectedClassId',
            'selectedSectionId',
            'date',
            'students',
            'attendances',
            'presentCount',
            'absentCount',
            'turnoutPct'
        ));
    }

    public function record(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
        ]);

        $date = $validated['date'];

        DB::transaction(function () use ($validated, $date) {
            foreach ($validated['attendance'] as $studentId => $status) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $date,
                    ],
                    [
                        'status' => in_array($status, ['present', 'absent', 'late', 'half_day']) ? $status : 'present',
                    ]
                );
            }

            AuditLog::record('student_rollcall_recorded', null, null, [
                'class_id' => $validated['school_class_id'],
                'section_id' => $validated['section_id'],
                'date' => $date,
                'count' => count($validated['attendance']),
            ]);
        });

        return redirect()->route('admin.academics.student-attendance.index', [
            'class_id' => $validated['school_class_id'],
            'section_id' => $validated['section_id'],
            'date' => $date,
        ])->with('success', 'Student roll-call attendance recorded successfully.');
    }
}
