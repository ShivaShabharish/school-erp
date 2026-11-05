<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\Timetable;
use App\Services\TimetableService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class TimetableController extends Controller
{
    public function __construct(
        protected TimetableService $timetableService
    ) {}

    public function index(Request $request): View
    {
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        // Include all teaching faculty & instructors across academic departments
        $staffMembers = Staff::with('user')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNotIn('department', ['Transport', 'School Store', 'Administration', 'Accounts & Finance'])
                      ->orWhere('designation', 'like', '%Teacher%')
                      ->orWhere('designation', 'like', '%PGT%')
                      ->orWhere('designation', 'like', '%TGT%')
                      ->orWhere('designation', 'like', '%Instructor%');
            })
            ->orderBy('id')
            ->get();

        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::with('user')->where('status', 'active')->orderBy('id')->get();
        }

        $subjects = Subject::orderBy('name')->get();

        // Default to Class 10 (or first class) with its first section
        $defaultClass = $classes->first(fn($c) => str_contains(strtolower($c->name), '10')) ?? $classes->first();
        $selectedClassId = $request->get('class_id', $defaultClass?->id ?? null);
        $selectedClass = $classes->firstWhere('id', $selectedClassId) ?? $defaultClass;
        $selectedSectionId = $request->get('section_id', $selectedClass?->sections->first()?->id ?? null);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $periods = [1, 2, 3, 4, 5, 6, 7, 8];

        $slots = Timetable::with(['subject', 'teacher.user'])
            ->where('school_class_id', $selectedClassId)
            ->where('section_id', $selectedSectionId)
            ->get()
            ->keyBy(function ($item) {
                return $item->day_of_week . '_' . $item->period_number;
            });

        return view('academics.timetables.index', compact(
            'classes',
            'staffMembers',
            'subjects',
            'selectedClassId',
            'selectedSectionId',
            'days',
            'periods',
            'slots'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday'],
            'period_number' => ['required', 'integer', 'between:1,8'],
            'room_no' => ['nullable', 'string', 'max:30'],
        ]);

        try {
            $this->timetableService->allocateSlot($validated);

            return redirect()->route('admin.academics.timetables.index', [
                'class_id' => $validated['school_class_id'],
                'section_id' => $validated['section_id'],
            ])->with('success', 'Timetable period slot assigned successfully.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error scheduling timetable: ' . $e->getMessage());
        }
    }
}
