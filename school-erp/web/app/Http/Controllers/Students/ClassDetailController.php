<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\View\View;

class ClassDetailController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::with(['sections' => function ($q) {
            $q->withCount(['students' => function ($sq) {
                $sq->where('status', 'active');
            }]);
        }])->orderBy('order_sequence')->get();

        $totalStrength = Student::where('status', 'active')->count();
        $totalSections = Section::count();

        return view('students.classes', compact('classes', 'totalStrength', 'totalSections'));
    }

    public function show(SchoolClass $class, Section $section): View
    {
        $today = Carbon::today()->toDateString();

        $students = Student::with(['parent'])
            ->where('school_class_id', $class->id)
            ->where('section_id', $section->id)
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        $totalStrength = $students->count();
        $presentCount = StudentAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $today)
            ->where('status', 'present')
            ->count();

        $attendancePct = $totalStrength > 0 ? round(($presentCount / $totalStrength) * 100, 1) : 95.0;

        return view('students.class_show', compact(
            'class',
            'section',
            'students',
            'totalStrength',
            'presentCount',
            'attendancePct'
        ));
    }
}
