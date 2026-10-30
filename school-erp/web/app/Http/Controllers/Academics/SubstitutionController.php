<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Substitution;
use App\Services\TimetableService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubstitutionController extends Controller
{
    public function __construct(
        protected TimetableService $timetableService
    ) {}

    public function index(Request $request): View
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        $suggestions = $this->timetableService->getSuggestedSubstitutionsForAbsentTeachers($date);

        $activeSubstitutions = Substitution::with(['timetable.schoolClass', 'timetable.section', 'timetable.subject', 'originalTeacher.user', 'substituteTeacher.user'])
            ->where('date', $date)
            ->get();

        return view('academics.substitutions.index', compact('suggestions', 'activeSubstitutions', 'date'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timetable_id' => ['required', 'exists:timetables,id'],
            'substitute_staff_id' => ['required', 'exists:staff,id'],
            'date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $this->timetableService->assignSubstitution(
            timetableId: $validated['timetable_id'],
            substituteStaffId: $validated['substitute_staff_id'],
            date: $validated['date'] ?? null,
            remarks: $validated['remarks'] ?? null
        );

        return redirect()->route('admin.academics.substitutions.index', ['date' => $validated['date'] ?? Carbon::today()->toDateString()])
            ->with('success', 'Substitute teacher assigned successfully.');
    }
}
