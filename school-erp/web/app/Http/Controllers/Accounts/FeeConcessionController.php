<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeConcession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeConcessionController extends Controller
{
    public function __construct(
        protected FeeService $feeService
    ) {}

    public function index(): View
    {
        $currentYear = AcademicYear::current();
        $concessions = FeeConcession::with(['student.schoolClass', 'student.section', 'approver'])
            ->where('academic_year_id', $currentYear?->id)
            ->orderByDesc('id')
            ->paginate(15);

        $classes = SchoolClass::with(['sections', 'students' => function ($q) {
            $q->where('status', 'active')->orderBy('first_name');
        }])->orderBy('order_sequence')->get();

        $totalWaiverAmount = FeeConcession::where('academic_year_id', $currentYear?->id)
            ->where('concession_type', 'fixed')
            ->sum('amount');
        $beneficiaryCount = FeeConcession::where('academic_year_id', $currentYear?->id)
            ->distinct('student_id')
            ->count('student_id');

        return view('accounts.fees.concessions', compact(
            'concessions',
            'classes',
            'currentYear',
            'totalWaiverAmount',
            'beneficiaryCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:100'],
            'concession_type' => ['required', 'in:percentage,fixed'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $concession = $this->feeService->applyConcession($validated, auth()->user());

        return redirect()->route('accounts.fees.concessions')
            ->with('success', "Concession '{$concession->title}' applied successfully for {$concession->student?->full_name}.");
    }
}
