<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeStructureController extends Controller
{
    public function __construct(
        protected FeeService $feeService
    ) {}

    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $classes = SchoolClass::with(['sections', 'feeStructures' => function ($q) use ($currentYear) {
            $q->where('academic_year_id', $currentYear?->id);
        }])->orderBy('order_sequence')->get();

        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedClass = $classes->firstWhere('id', $selectedClassId) ?? $classes->first();

        $structures = FeeStructure::where('school_class_id', $selectedClassId)
            ->where('academic_year_id', $currentYear?->id)
            ->orderBy('term')
            ->get();

        $term1Total = $structures->where('term', 'Term 1')->sum('amount');
        $term2Total = $structures->where('term', 'Term 2')->sum('amount');
        $term3Total = $structures->where('term', 'Term 3')->sum('amount');
        $annualTotal = $structures->sum('amount');

        return view('accounts.fees.index', compact(
            'classes',
            'selectedClass',
            'structures',
            'currentYear',
            'term1Total',
            'term2Total',
            'term3Total',
            'annualTotal'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:100'],
            'term' => ['required', 'in:Term 1,Term 2,Term 3,Annual'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
        ]);

        $structure = FeeStructure::create($validated);

        // Auto-allocate this new head to current active students in the class
        $allocatedCount = $this->feeService->allocateFeeStructureToClass($structure);

        AuditLog::record('fee_structure_created', $structure, null, [
            'class_id' => $structure->school_class_id,
            'name' => $structure->name,
            'amount' => $structure->amount,
            'students_allocated' => $allocatedCount,
        ]);

        return redirect()->route('accounts.fees.index', ['class_id' => $structure->school_class_id])
            ->with('success', "Fee component '{$structure->name}' created and allocated to {$allocatedCount} students.");
    }
}
