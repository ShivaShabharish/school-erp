<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(): View
    {
        $exams = Exam::with(['academicYear', 'schedules.schoolClass', 'schedules.subject'])
            ->latest('id')
            ->get();

        $classes = SchoolClass::orderBy('order_sequence')->get();
        $subjects = Subject::orderBy('name')->get();
        $academicYears = AcademicYear::all();

        return view('academics.exams.index', compact('exams', 'classes', 'subjects', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'exam_type' => ['required', 'in:quarterly,half_yearly,annual,mid_term,unit_test'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $exam = Exam::create([
            ...$validated,
            'status' => 'scheduled',
        ]);

        AuditLog::record('exam_created', $exam, null, [
            'name' => $exam->name,
            'exam_type' => $exam->exam_type,
        ]);

        return redirect()->route('admin.academics.exams.index')
            ->with('success', "Examination '{$exam->name}' created successfully.");
    }

    public function reportCardPdf(Exam $exam, Student $student): Response
    {
        $data = $this->reportService->compileStudentReportCard($exam, $student);

        $pdf = Pdf::loadView('academics.exams.report_card_pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "ReportCard-{$student->admission_no}-{$exam->name}.pdf";

        return $pdf->download($filename);
    }
}
