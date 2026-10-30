<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveQuota;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class StaffAttendanceTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role', 'principal')->first() ?? User::factory()->create([
            'role' => 'principal',
            'email' => 'principal@school.test',
        ]);
    }

    public function test_admin_can_view_staff_directory(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Faculty & Staff Directory', false);
        $response->assertSee('Total Active Personnel');
    }

    public function test_admin_can_onboard_new_staff(): void
    {
        $admin = $this->getAdminUser();
        $code = 'STF-TEST-' . rand(1000, 9999);
        $email = 'faculty.' . rand(1000, 9999) . '@school.test';

        $payload = [
            'name' => 'Dr. R. Senthamizhan',
            'email' => $email,
            'phone' => '9842199888',
            'employee_code' => $code,
            'designation' => 'Senior Tamil Lecturer',
            'department' => 'Teaching',
            'qualification' => 'Ph.D., M.A., B.Ed.',
            'gender' => 'male',
            'dob' => '1982-07-14',
            'joining_date' => '2026-06-01',
            'basic_salary' => '48000',
            'bank_account_no' => '987654321012',
            'bank_name' => 'Indian Bank',
            'bank_ifsc' => 'IDIB000P042',
            'address' => 'Gandhi Nagar, Pudukkottai',
        ];

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), $payload);

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('staff', [
            'employee_code' => $code,
            'designation' => 'Senior Tamil Lecturer',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'teacher',
        ]);
    }

    public function test_admin_can_view_360_staff_profile(): void
    {
        $admin = $this->getAdminUser();
        $staff = Staff::first();

        $response = $this->actingAs($admin)->get(route('admin.staff.show', $staff));

        $response->assertStatus(200);
        $response->assertSee($staff->employee_code);
        $response->assertSee('Personnel Information & Contact', false);
        $response->assertSee('Standard Monthly Compensation Structure');
    }

    public function test_biometric_api_punch_endpoint(): void
    {
        $staff = Staff::first();

        // 1. Unauthorized request without valid header
        $unauthRes = $this->postJson(route('api.v1.biometric.punch'), [
            'staff_id' => $staff->id,
            'device_id' => 'BIO-GATE-01',
        ]);
        $unauthRes->assertStatus(401);

        // 2. Authorized Punch with header
        $authRes = $this->withHeaders([
            'X-Biometric-Key' => 'SCHOOL_BIO_SEC_2026',
        ])->postJson(route('api.v1.biometric.punch'), [
            'staff_id' => $staff->id,
            'device_id' => 'BIO-GATE-01',
            'timestamp' => '2026-10-30 08:20:00', // On time
        ]);

        $authRes->assertStatus(200);
        $authRes->assertJsonFragment([
            'success' => true,
            'staff_id' => $staff->id,
            'status' => 'present',
            'device_id' => 'BIO-GATE-01',
        ]);

        // 3. Subsequent Punch (Punch-out)
        $punchOutRes = $this->withHeaders([
            'X-Biometric-Key' => 'SCHOOL_BIO_SEC_2026',
        ])->postJson(route('api.v1.biometric.punch'), [
            'staff_id' => $staff->id,
            'device_id' => 'BIO-GATE-01',
            'timestamp' => '2026-10-30 16:35:00',
        ]);

        $punchOutRes->assertStatus(200);
        $punchOutRes->assertJsonFragment([
            'action' => 'punch_out',
        ]);
    }

    public function test_admin_can_simulate_biometric_punch(): void
    {
        $admin = $this->getAdminUser();
        $staff = Staff::skip(1)->first();

        // View simulator page
        $pageRes = $this->actingAs($admin)->get(route('admin.staff.biometric-simulator'));
        $pageRes->assertStatus(200);
        $pageRes->assertSee('Biometric Machine Punch Simulator');

        // Post simulated punch
        $simRes = $this->actingAs($admin)->post(route('admin.staff.simulate-punch'), [
            'staff_id' => $staff->id,
            'device_id' => 'BIO-STAFF-02',
            'timestamp' => '2026-10-30 08:45:00', // Late arrival
        ]);

        $simRes->assertRedirect(route('admin.staff.biometric-simulator'));
        $this->assertDatabaseHas('staff_attendances', [
            'staff_id' => $staff->id,
            'device_id' => 'BIO-STAFF-02',
            'status' => 'late',
        ]);
    }

    public function test_admin_can_view_daily_attendance_log(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance'));

        $response->assertStatus(200);
        $response->assertSee('Faculty Biometric Attendance Logs');
        $response->assertSee('Total Staff');
        $response->assertSee('Late Arrivals');
    }

    public function test_leave_workflow_apply_and_approve(): void
    {
        $admin = $this->getAdminUser();
        $staff = Staff::first();
        $leaveType = LeaveType::where('code', 'CL')->first();

        $randomDay = rand(10, 20);
        $startDate = "2026-11-{$randomDay}";
        $endDate = "2026-11-" . ($randomDay + 1);

        // 1. Submit leave request
        $applyRes = $this->actingAs($admin)->post(route('admin.staff.leaves.apply'), [
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Attending educational seminar in Chennai',
        ]);

        $applyRes->assertRedirect(route('admin.staff.leaves.index'));
        $application = LeaveApplication::where('staff_id', $staff->id)
            ->where('start_date', $startDate)
            ->latest('id')
            ->first();

        $this->assertNotNull($application);
        $this->assertEquals('pending', $application->status);

        // 2. Approve leave request
        $approveRes = $this->actingAs($admin)->post(route('admin.staff.leaves.approve', $application));
        $approveRes->assertRedirect(route('admin.staff.leaves.index'));

        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertEquals($admin->id, $application->approved_by);

        // Verify attendance record created as 'leave'
        $this->assertDatabaseHas('staff_attendances', [
            'staff_id' => $staff->id,
            'date' => $startDate,
            'status' => 'leave',
        ]);
    }

    public function test_payroll_generation_and_payslip_pdf_download(): void
    {
        $admin = $this->getAdminUser();
        $month = Carbon::now()->format('Y-m');

        // View payroll sheet
        $response = $this->actingAs($admin)->get(route('admin.staff.payroll.index', ['month' => $month]));
        $response->assertStatus(200);
        $response->assertSee('Monthly Salary Sheet & Payslips', false);
        $response->assertSee('Total Net Disbursed');

        $payroll = Payroll::first();
        $this->assertNotNull($payroll);

        // Download payslip PDF
        $pdfRes = $this->actingAs($admin)->get(route('admin.staff.payroll.payslip', $payroll));
        $pdfRes->assertStatus(200);
        $pdfRes->assertHeader('content-type', 'application/pdf');
    }
}
