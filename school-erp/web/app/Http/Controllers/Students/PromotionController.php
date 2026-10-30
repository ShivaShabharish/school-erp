<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentPromotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();
        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        $currentYear = AcademicYear::current();

        $selectedClassId = $request->get('from_class_id');
        $selectedSectionId = $request->get('from_section_id');

        $students = collect();
        if ($selectedClassId && $selectedSectionId) {
            $students = Student::where('school_class_id', $selectedClassId)
                ->where('section_id', $selectedSectionId)
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();
        }

        $recentPromotions = StudentPromotion::with(['student', 'fromClass', 'fromSection', 'toClass', 'toSection', 'academicYear'])
            ->latest()
            ->limit(10)
            ->get();

        return view('students.promotion', compact(
            'classes',
            'academicYears',
            'currentYear',
            'selectedClassId',
            'selectedSectionId',
            'students',
            'recentPromotions'
        ));
    }

    public function promote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_class_id' => ['required', 'exists:school_classes,id'],
            'from_section_id' => ['required', 'exists:sections,id'],
            'to_class_id' => ['required', 'exists:school_classes,id'],
            'to_section_id' => ['required', 'exists:sections,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        $promotedCount = 0;

        DB::transaction(function () use ($validated, &$promotedCount) {
            $user = auth()->user();

            foreach ($validated['student_ids'] as $studentId) {
                $student = Student::find($studentId);

                StudentPromotion::create([
                    'student_id' => $student->id,
                    'from_class_id' => $validated['from_class_id'],
                    'from_section_id' => $validated['from_section_id'],
                    'to_class_id' => $validated['to_class_id'],
                    'to_section_id' => $validated['to_section_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'promoted_by' => $user?->id,
                    'status' => 'promoted',
                ]);

                $student->update([
                    'school_class_id' => $validated['to_class_id'],
                    'section_id' => $validated['to_section_id'],
                ]);

                $promotedCount++;
            }

            AuditLog::record('batch_promotion_executed', null, null, [
                'from_class_id' => $validated['from_class_id'],
                'to_class_id' => $validated['to_class_id'],
                'count' => $promotedCount,
            ]);
        });

        return redirect()->route('admin.students.promotion')
            ->with('success', "Batch promotion completed: {$promotedCount} students successfully promoted to the next academic level!");
    }
}
