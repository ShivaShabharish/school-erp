<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\PtmSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PtmController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $selectedClassId = $request->get('class_id');

        $query = PtmSchedule::with(['schoolClass', 'section', 'creator'])
            ->where('academic_year_id', $currentYear?->id);

        if ($selectedClassId) {
            $query->where('school_class_id', $selectedClassId);
        }

        $schedules = $query->orderBy('meeting_date')->paginate(10);
        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();

        $upcomingCount = PtmSchedule::where('academic_year_id', $currentYear?->id)
            ->where('meeting_date', '>=', now()->toDateString())
            ->count();

        return view('communication.ptm.index', compact(
            'schedules',
            'classes',
            'currentYear',
            'selectedClassId',
            'upcomingCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'meeting_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'venue' => ['required', 'string', 'max:100'],
            'agenda' => ['nullable', 'string', 'max:500'],
        ]);

        $ptm = PtmSchedule::create([
            ...$validated,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        AuditLog::record('ptm_scheduled', $ptm, null, [
            'class_id' => $ptm->school_class_id,
            'section_id' => $ptm->section_id,
            'meeting_date' => $ptm->meeting_date->toDateString(),
        ]);

        return redirect()->route('admin.communication.ptm.index')
            ->with('success', "Parent-Teacher Meeting scheduled for {$ptm->schoolClass?->name} - {$ptm->section?->name}.");
    }
}
