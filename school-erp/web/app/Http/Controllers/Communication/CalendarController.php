<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = AcademicYear::current();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $events = CalendarEvent::with('creator')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function ($sq) use ($startDate, $endDate) {
                      $sq->whereNotNull('end_date')
                         ->where('start_date', '<=', $endDate->toDateString())
                         ->where('end_date', '>=', $startDate->toDateString());
                  });
            })
            ->orderBy('start_date')
            ->get();

        $upcomingHolidays = CalendarEvent::where('is_holiday', true)
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $totalHolidaysInYear = CalendarEvent::where('academic_year_id', $currentYear?->id)
            ->where('is_holiday', true)
            ->count();

        $totalFunctions = CalendarEvent::where('academic_year_id', $currentYear?->id)
            ->where('event_type', 'function')
            ->count();

        return view('communication.calendar.index', compact(
            'events',
            'month',
            'year',
            'currentYear',
            'upcomingHolidays',
            'totalHolidaysInYear',
            'totalFunctions'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:150'],
            'event_type' => ['required', 'in:holiday,exam,function,ptm,sports,academic'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_holiday' => ['nullable', 'boolean'],
            'target_audience' => ['required', 'in:all,students,staff,parents'],
        ]);

        $event = CalendarEvent::create([
            ...$validated,
            'is_holiday' => !empty($validated['is_holiday']) || $validated['event_type'] === 'holiday',
            'created_by' => auth()->id(),
        ]);

        AuditLog::record('calendar_event_created', $event, null, [
            'title' => $event->title,
            'start_date' => $event->start_date->toDateString(),
            'event_type' => $event->event_type,
        ]);

        return redirect()->route('admin.communication.calendar.index', [
            'month' => $event->start_date->month,
            'year' => $event->start_date->year,
        ])->with('success', "Event '{$event->title}' scheduled on school calendar.");
    }

    public function destroy(CalendarEvent $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();

        AuditLog::record('calendar_event_deleted', null, null, ['title' => $title]);

        return back()->with('success', "Event '{$title}' removed from calendar.");
    }
}
