<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ExamSchedule;
use App\Models\Mark;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarksController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(ExamSchedule $schedule): View
    {
        $schedule->load(['exam', 'schoolClass.sections', 'subject']);

        $students = Student::where('school_class_id', $schedule->school_class_id)
            ->where('status', 'active')
            ->orderBy('section_id')
            ->orderBy('roll_number')
            ->get();

        $existingMarks = Mark::where('exam_schedule_id', $schedule->id)
            ->get()
            ->keyBy('student_id');

        return view('academics.exams.marks', compact('schedule', 'students', 'existingMarks'));
    }

    public function saveMarks(ExamSchedule $schedule, Request $request): RedirectResponse
    {
        $marksData = $request->input('marks', []);

        DB::transaction(function () use ($schedule, $marksData) {
            foreach ($marksData as $studentId => $entry) {
                $obtained = isset($entry['marks_obtained']) && $entry['marks_obtained'] !== ''
                    ? (float) $entry['marks_obtained']
                    : 0.0;
                $isAbsent = !empty($entry['is_absent']);

                $gradeInfo = $this->reportService->calculateGrade($obtained, (float) $schedule->max_marks);

                Mark::updateOrCreate(
                    [
                        'exam_schedule_id' => $schedule->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $obtained,
                        'grade' => $isAbsent ? 'AB' : $gradeInfo['grade'],
                        'grade_point' => $isAbsent ? 0.0 : $gradeInfo['point'],
                        'is_absent' => $isAbsent,
                        'remarks' => $entry['remarks'] ?? ($isAbsent ? 'Absent for Examination' : $gradeInfo['remark']),
                    ]
                );
            }

            AuditLog::record('marks_entered', $schedule, null, [
                'exam' => $schedule->exam?->name,
                'class' => $schedule->schoolClass?->name,
                'subject' => $schedule->subject?->name,
                'count' => count($marksData),
            ]);
        });

        return redirect()->route('admin.academics.exams.marks.index', $schedule)
            ->with('success', 'Student examination marks and CCE grades saved successfully.');
    }
}
