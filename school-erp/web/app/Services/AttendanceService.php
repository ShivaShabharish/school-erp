<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AttendanceService
{
    /**
     * Process a biometric machine punch.
     */
    public function recordBiometricPunch(string $deviceId, int|string $staffId, ?string $timestamp = null): array
    {
        $staff = Staff::with('user')->find($staffId);
        if (!$staff) {
            // Check by employee_code
            $staff = Staff::with('user')->where('employee_code', $staffId)->first();
        }

        if (!$staff) {
            throw new InvalidArgumentException("Staff member with ID/Code [{$staffId}] not found.");
        }

        $punchTime = $timestamp ? Carbon::parse($timestamp) : Carbon::now();
        $date = $punchTime->toDateString();
        $timeStr = $punchTime->format('H:i:s');

        return DB::transaction(function () use ($staff, $deviceId, $date, $timeStr, $punchTime) {
            // Find existing attendance record (handling both YYYY-MM-DD and YYYY-MM-DD 00:00:00 SQLite representations)
            $attendance = StaffAttendance::where('staff_id', $staff->id)
                ->where(function ($q) use ($date) {
                    $q->where('date', $date)
                      ->orWhere('date', 'LIKE', $date . '%')
                      ->orWhereDate('date', $date);
                })
                ->first();

            $isFirstPunch = !$attendance || empty($attendance->punch_in_time);
            $action = 'punch_in';

            if ($isFirstPunch) {
                // 08:30 AM is standard reporting cutoff
                $cutoff = Carbon::parse($date . ' 08:30:00');
                $status = $punchTime->lte($cutoff) ? 'present' : 'late';

                if (!$attendance) {
                    $attendance = StaffAttendance::updateOrCreate(
                        [
                            'staff_id' => $staff->id,
                            'date' => $date,
                        ],
                        [
                            'punch_in_time' => $timeStr,
                            'status' => $status,
                            'device_id' => $deviceId,
                        ]
                    );
                } else {
                    $attendance->date = $date;
                    $attendance->punch_in_time = $timeStr;
                    $attendance->status = $status;
                    $attendance->device_id = $deviceId;
                    $attendance->save();
                }
            } else {
                // Subsequent punch sets or updates punch_out_time
                $attendance->date = $date;
                $attendance->punch_out_time = $timeStr;
                $attendance->device_id = $deviceId;
                $attendance->save();
                $action = 'punch_out';
            }

            AuditLog::record('biometric_punch_recorded', $attendance, null, [
                'staff_code' => $staff->employee_code,
                'name' => $staff->full_name,
                'action' => $action,
                'device_id' => $deviceId,
                'timestamp' => $punchTime->toDateTimeString(),
                'status' => $attendance->status,
            ]);

            return [
                'success' => true,
                'staff_id' => $staff->id,
                'employee_code' => $staff->employee_code,
                'staff_name' => $staff->full_name,
                'action' => $action,
                'punch_in_time' => $attendance->punch_in_time,
                'punch_out_time' => $attendance->punch_out_time,
                'status' => $attendance->status,
                'device_id' => $deviceId,
                'timestamp' => $punchTime->toIso8601String(),
                'message' => "Biometric {$action} registered successfully for {$staff->full_name}.",
            ];
        });
    }

    /**
     * Get real-time summary of today's staff attendance.
     */
    public function getTodayAttendanceSummary(): array
    {
        $today = Carbon::today()->toDateString();
        $totalStaff = Staff::where('status', 'active')->count();

        $attendances = StaffAttendance::where('date', $today)->get();
        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $onLeave = $attendances->where('status', 'leave')->count();
        $totalPunched = $present + $late;
        $absent = max(0, $totalStaff - ($totalPunched + $onLeave));

        $turnoutPct = $totalStaff > 0 ? round(($totalPunched / $totalStaff) * 100, 1) : 0;

        return [
            'total_staff' => $totalStaff,
            'present' => $present,
            'late' => $late,
            'on_leave' => $onLeave,
            'absent' => $absent,
            'total_punched' => $totalPunched,
            'turnout_pct' => $turnoutPct,
        ];
    }
}
