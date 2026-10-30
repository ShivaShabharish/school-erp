<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdCardController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSectionId = $request->get('section_id', $classes->first()?->sections->first()?->id);

        $previewStudents = Student::with(['schoolClass', 'section'])
            ->where('school_class_id', $selectedClassId)
            ->when($selectedSectionId, fn($q) => $q->where('section_id', $selectedSectionId))
            ->where('status', 'active')
            ->take(8)
            ->get();

        $previewStaff = Staff::with('user')
            ->where('status', 'active')
            ->take(8)
            ->get();

        return view('communication.id_cards.index', compact(
            'classes',
            'selectedClassId',
            'selectedSectionId',
            'previewStudents',
            'previewStaff',
            'currentYear'
        ));
    }

    public function printStudents(Request $request): View
    {
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        $students = Student::with(['schoolClass', 'section'])
            ->when($classId, fn($q) => $q->where('school_class_id', $classId))
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->get();

        $currentYear = AcademicYear::current();

        return view('communication.id_cards.print_students', compact('students', 'currentYear'));
    }

    public function printStaff(): View
    {
        $staffMembers = Staff::with('user')
            ->where('status', 'active')
            ->orderBy('employee_code')
            ->get();

        $currentYear = AcademicYear::current();

        return view('communication.id_cards.print_staff', compact('staffMembers', 'currentYear'));
    }
}
