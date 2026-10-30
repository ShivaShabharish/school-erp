<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveQuota;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeaveService
{
    /**
     * Submit a leave application.
     */
    public function applyLeave(Staff $staff, array $data): LeaveApplication
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->lt($startDate)) {
            throw new InvalidArgumentException("End date cannot precede start date.");
        }

        $days = (float) ($startDate->diffInDays($endDate) + 1);

        return DB::transaction(function () use ($staff, $data, $startDate, $endDate, $days) {
            $application = LeaveApplication::create([
                'staff_id' => $staff->id,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days_count' => $days,
                'reason' => $data['reason'],
                'status' => 'pending',
            ]);

            AuditLog::record('leave_applied', $application, null, [
                'staff_code' => $staff->employee_code,
                'days' => $days,
                'reason' => $data['reason'],
            ]);

            return $application;
        });
    }

    /**
     * Approve a leave application.
     */
    public function approveLeave(LeaveApplication $application, User $approver): bool
    {
        return DB::transaction(function () use ($application, $approver) {
            $application->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
            ]);

            // Deduct days from staff leave quota
            $currentYear = AcademicYear::current();
            if ($currentYear) {
                $quota = StaffLeaveQuota::where('staff_id', $application->staff_id)
                    ->where('leave_type_id', $application->leave_type_id)
                    ->where('academic_year_id', $currentYear->id)
                    ->first();

                if ($quota) {
                    $quota->increment('used_days', $application->days_count);
                }
            }

            // Mark attendance records as 'leave'
            $period = CarbonPeriod::create($application->start_date, $application->end_date);
            foreach ($period as $dt) {
                StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $application->staff_id,
                        'date' => $dt->toDateString(),
                    ],
                    [
                        'status' => 'leave',
                    ]
                );
            }

            AuditLog::record('leave_approved', $application, null, [
                'approver' => $approver->name,
                'staff_id' => $application->staff_id,
            ]);

            return true;
        });
    }

    /**
     * Reject a leave application.
     */
    public function rejectLeave(LeaveApplication $application, User $approver, ?string $remarks = null): bool
    {
        return DB::transaction(function () use ($application, $approver, $remarks) {
            $application->update([
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'remarks' => $remarks ?: 'Leave request declined by administrator.',
            ]);

            AuditLog::record('leave_rejected', $application, null, [
                'approver' => $approver->name,
                'remarks' => $remarks,
            ]);

            return true;
        });
    }

    /**
     * Initialize standard leave quotas for a staff member.
     */
    public function initializeQuotas(Staff $staff, AcademicYear $academicYear): void
    {
        $types = LeaveType::all();
        foreach ($types as $type) {
            StaffLeaveQuota::firstOrCreate(
                [
                    'staff_id' => $staff->id,
                    'leave_type_id' => $type->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'allocated_days' => $type->days_per_year,
                    'used_days' => 0.0,
                ]
            );
        }
    }
}
