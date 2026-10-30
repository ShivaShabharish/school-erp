<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Substitution;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TimetableService
{
    /**
     * Check if a teacher has a timetable clash at a given day and period.
     */
    public function checkTeacherClash(int $staffId, string $day, int $period, ?int $ignoreId = null): ?Timetable
    {
        $query = Timetable::with(['schoolClass', 'section', 'subject'])
            ->where('staff_id', $staffId)
            ->where('day_of_week', strtolower($day))
            ->where('period_number', $period);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first();
    }

    /**
     * Check if a room is double-booked at a given day and period.
     */
    public function checkRoomClash(string $roomNo, string $day, int $period, ?int $ignoreId = null): ?Timetable
    {
        if (empty($roomNo)) {
            return null;
        }

        $query = Timetable::with(['schoolClass', 'section'])
            ->where('room_no', $roomNo)
            ->where('day_of_week', strtolower($day))
            ->where('period_number', $period);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first();
    }

    /**
     * Allocate a timetable period slot with clash verification.
     */
    public function allocateSlot(array $data): Timetable
    {
        $teacherClash = $this->checkTeacherClash(
            staffId: $data['staff_id'],
            day: $data['day_of_week'],
            period: (int) $data['period_number']
        );

        if ($teacherClash) {
            $teacher = Staff::find($data['staff_id']);
            throw new InvalidArgumentException(
                "Teacher Clash Detected: {$teacher->full_name} is already assigned to {$teacherClash->schoolClass->name} ({$teacherClash->section->name}) during Period {$data['period_number']} on " . ucfirst($data['day_of_week']) . "."
            );
        }

        if (!empty($data['room_no'])) {
            $roomClash = $this->checkRoomClash(
                roomNo: $data['room_no'],
                day: $data['day_of_week'],
                period: (int) $data['period_number']
            );

            if ($roomClash) {
                throw new InvalidArgumentException(
                    "Room Conflict: Room {$data['room_no']} is already occupied by {$roomClash->schoolClass->name} ({$roomClash->section->name}) during Period {$data['period_number']} on " . ucfirst($data['day_of_week']) . "."
                );
            }
        }

        return DB::transaction(function () use ($data) {
            $slot = Timetable::updateOrCreate(
                [
                    'school_class_id' => $data['school_class_id'],
                    'section_id' => $data['section_id'],
                    'day_of_week' => strtolower($data['day_of_week']),
                    'period_number' => (int) $data['period_number'],
                ],
                [
                    'subject_id' => $data['subject_id'],
                    'staff_id' => $data['staff_id'],
                    'start_time' => $data['start_time'] ?? '09:00:00',
                    'end_time' => $data['end_time'] ?? '09:45:00',
                    'room_no' => $data['room_no'] ?? null,
                ]
            );

            AuditLog::record('timetable_slot_allocated', $slot, null, [
                'class_id' => $slot->school_class_id,
                'section_id' => $slot->section_id,
                'day' => $slot->day_of_week,
                'period' => $slot->period_number,
            ]);

            return $slot;
        });
    }

    /**
     * Suggest automatic substitute teachers for absent faculty today.
     */
    public function getSuggestedSubstitutionsForAbsentTeachers(?string $date = null): array
    {
        $checkDate = $date ? Carbon::parse($date) : Carbon::today();
        $dayOfWeek = strtolower($checkDate->format('l'));
        $dateStr = $checkDate->toDateString();

        // Find absent or on-leave staff today
        $absentStaffIds = StaffAttendance::where('date', $dateStr)
            ->whereIn('status', ['leave', 'on_leave', 'absent'])
            ->pluck('staff_id')
            ->toArray();

        // Also include active staff who haven't punched in at all
        $allActiveStaff = Staff::where('status', 'active')->where('department', 'Teaching')->pluck('id')->toArray();
        $punchedStaffIds = StaffAttendance::where('date', $dateStr)
            ->whereNotNull('punch_in_time')
            ->pluck('staff_id')
            ->toArray();

        $notPunchedStaffIds = array_diff($allActiveStaff, $punchedStaffIds);
        $totalAbsentIds = array_unique(array_merge($absentStaffIds, $notPunchedStaffIds));

        if (empty($totalAbsentIds)) {
            return [];
        }

        // Get all periods scheduled for absent teachers on this day of week
        $affectedSlots = Timetable::with(['schoolClass', 'section', 'subject', 'teacher.user'])
            ->whereIn('staff_id', $totalAbsentIds)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('period_number')
            ->get();

        $substitutions = [];

        foreach ($affectedSlots as $slot) {
            // Check if already assigned
            $existingSub = Substitution::with('substituteTeacher.user')
                ->where('date', $dateStr)
                ->where('timetable_id', $slot->id)
                ->first();

            // Find free teachers during this day & period who are PRESENT today
            $busyStaffIds = Timetable::where('day_of_week', $dayOfWeek)
                ->where('period_number', $slot->period_number)
                ->pluck('staff_id')
                ->toArray();

            $freeEligibleTeachers = Staff::with('user')
                ->where('status', 'active')
                ->where('department', 'Teaching')
                ->whereIn('id', $punchedStaffIds) // Must be present today
                ->whereNotIn('id', $busyStaffIds)  // Free this period
                ->where('id', '!=', $slot->staff_id)
                ->get();

            // Prioritize teachers of same subject/department
            $sortedSuggestions = $freeEligibleTeachers->sortByDesc(function ($t) use ($slot) {
                return $t->department === $slot->subject->department ? 10 : 1;
            })->values();

            $substitutions[] = [
                'timetable' => $slot,
                'existing_substitution' => $existingSub,
                'suggested_candidates' => $sortedSuggestions,
                'period' => $slot->period_number,
                'subject' => $slot->subject->name,
                'class' => "{$slot->schoolClass->name} - {$slot->section->name}",
                'absent_teacher' => $slot->teacher->full_name,
            ];
        }

        return $substitutions;
    }

    /**
     * Assign a substitute teacher to a slot.
     */
    public function assignSubstitution(int $timetableId, int $substituteStaffId, ?string $date = null, ?string $remarks = null): Substitution
    {
        $dateStr = $date ?: Carbon::today()->toDateString();
        $timetable = Timetable::findOrFail($timetableId);

        return DB::transaction(function () use ($timetable, $substituteStaffId, $dateStr, $remarks) {
            $sub = Substitution::updateOrCreate(
                [
                    'date' => $dateStr,
                    'timetable_id' => $timetable->id,
                ],
                [
                    'original_staff_id' => $timetable->staff_id,
                    'substitute_staff_id' => $substituteStaffId,
                    'status' => 'assigned',
                    'remarks' => $remarks ?: 'Automatic substitution assigned',
                    'assigned_by' => auth()->id(),
                ]
            );

            AuditLog::record('substitution_assigned', $sub, null, [
                'original_staff' => $timetable->staff_id,
                'substitute_staff' => $substituteStaffId,
                'date' => $dateStr,
            ]);

            return $sub;
        });
    }
}
