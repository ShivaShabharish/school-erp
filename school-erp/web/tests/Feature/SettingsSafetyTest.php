<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class SettingsSafetyTest extends TestCase
{
    protected User $admin;
    protected User $parent;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            ['name' => 'System Administrator', 'password' => bcrypt('Password@123'), 'role' => 'admin', 'is_active' => true]
        );

        $this->parent = User::firstOrCreate(
            ['email' => 'parent@school.test'],
            ['name' => 'School Parent', 'password' => bcrypt('Password@123'), 'role' => 'parent', 'is_active' => true]
        );

        $this->student = Student::first();
    }

    public function test_admin_can_view_settings_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Settings, Safety & API Gateway', false);
        $response->assertSee('School Profile & Identity', false);
    }

    public function test_admin_can_update_school_profile_and_audited(): void
    {
        $payload = [
            'school_name' => 'Test Subject Higher Secondary School Pudukkottai',
            'school_board' => 'Tamil Nadu State Board',
            'school_affiliation_no' => 'TN-EDU-2026-9999',
            'school_location' => 'Pudukkottai Town, Tamil Nadu',
            'contact_phone' => '+91 4322 999888',
            'contact_email' => 'admin@testsubjecthss.edu.in',
            'currency_symbol' => '₹',
            'receipt_prefix_fees' => 'SCH/2026-27/REC-',
            'receipt_prefix_store' => 'SCH/2026-27/STR-',
            'biometric_api_secret' => 'NEW-SEC-KEY-9842',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update-profile'), $payload);
        $response->assertRedirect(route('admin.settings.index', ['tab' => 'profile']));

        $this->assertEquals('Test Subject Higher Secondary School Pudukkottai', Setting::get('school_name'));
        $this->assertEquals('NEW-SEC-KEY-9842', Setting::get('biometric_api_secret'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'settings_profile_updated',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_create_and_switch_academic_year(): void
    {
        $sessionName = '2028-2029';
        AcademicYear::where('name', $sessionName)->delete();

        $postResponse = $this->actingAs($this->admin)->post(route('admin.settings.store-year'), [
            'name' => $sessionName,
            'start_date' => '2028-04-01',
            'end_date' => '2029-03-31',
        ]);
        $postResponse->assertRedirect(route('admin.settings.index', ['tab' => 'academic_years']));

        $createdYear = AcademicYear::where('name', $sessionName)->first();
        $this->assertNotNull($createdYear);
        $this->assertFalse((bool) $createdYear->is_current);

        // Switch to this year
        $switchResponse = $this->actingAs($this->admin)->post(route('admin.settings.switch-year', $createdYear));
        $switchResponse->assertRedirect(route('admin.settings.index', ['tab' => 'academic_years']));

        $createdYear->refresh();
        $this->assertTrue((bool) $createdYear->is_current);

        // Switch back to 2026-2027
        $defaultYear = AcademicYear::where('name', '2026-2027')->first();
        if ($defaultYear) {
            $this->actingAs($this->admin)->post(route('admin.settings.switch-year', $defaultYear));
        }
    }

    public function test_mobile_app_integration_switch_toggling(): void
    {
        Setting::set('app_integration_enabled', 'false');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.toggle-mobile-app'));
        $response->assertRedirect(route('admin.settings.index', ['tab' => 'safety_api']));

        $this->assertEquals('true', Setting::get('app_integration_enabled'));

        // Toggle back off
        $response2 = $this->actingAs($this->admin)->post(route('admin.settings.toggle-mobile-app'));
        $this->assertEquals('false', Setting::get('app_integration_enabled'));
    }

    public function test_api_returns_503_when_mobile_app_integration_disabled(): void
    {
        Setting::set('app_integration_enabled', 'false');

        $response = $this->postJson(route('api.v1.auth.token'), [
            'email' => 'parent@school.test',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(503);
        $response->assertJson([
            'status' => 'error',
            'code' => 'API_INTEGRATION_DISABLED',
        ]);
    }

    public function test_api_token_issuance_and_portal_access_when_enabled(): void
    {
        Setting::set('app_integration_enabled', 'true');

        // 1. Issue Token
        $tokenResponse = $this->postJson(route('api.v1.auth.token'), [
            'email' => 'parent@school.test',
            'password' => 'Password@123',
            'device_name' => 'TestDevice',
        ]);

        $tokenResponse->assertStatus(200);
        $tokenResponse->assertJsonStructure(['status', 'token', 'user']);

        $token = $tokenResponse->json('token');

        // 2. Access /api/v1/auth/user
        $userResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('api.v1.auth.user'));
        $userResponse->assertStatus(200);
        $userResponse->assertJsonPath('data.email', 'parent@school.test');

        // 3. Access Student Profile
        $profileResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('api.v1.student.profile', ['student_id' => $this->student->id]));
        $profileResponse->assertStatus(200);
        $profileResponse->assertJsonStructure(['status', 'data' => ['admission_number', 'full_name', 'class', 'section']]);

        // 4. Access Student Attendance
        $attResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('api.v1.student.attendance', ['student_id' => $this->student->id]));
        $attResponse->assertStatus(200);
        $attResponse->assertJsonStructure(['status', 'data' => ['summary' => ['attendance_percentage']]]);

        // 5. Access Student Fees
        $feesResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('api.v1.student.fees', ['student_id' => $this->student->id]));
        $feesResponse->assertStatus(200);
        $feesResponse->assertJsonStructure(['status', 'data' => ['overview' => ['total_assessed']]]);

        // 6. Access Announcements
        $annResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('api.v1.announcements.index'));
        $annResponse->assertStatus(200);
        $annResponse->assertJsonStructure(['status', 'data']);

        // 7. Revoke Token
        $revokeResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.v1.auth.revoke'));
        $revokeResponse->assertStatus(200);
        $revokeResponse->assertJson(['status' => 'success']);
    }

    public function test_admin_can_export_csv_and_sql_backup(): void
    {
        // 1. CSV Export
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.settings.export', ['type' => 'students']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        // 2. SQL Backup
        $sqlResponse = $this->actingAs($this->admin)->get(route('admin.settings.backup.sql'));
        $sqlResponse->assertStatus(200);
        $this->assertStringContainsString('application/sql', $sqlResponse->headers->get('content-type'));
    }

    public function test_admin_can_view_audit_logs(): void
    {
        AuditLog::record('test_security_audit_event', null, ['foo' => 'bar'], ['foo' => 'baz']);

        $response = $this->actingAs($this->admin)->get(route('admin.settings.audit-logs', [
            'action' => 'test_security_audit_event',
        ]));

        $response->assertStatus(200);
        $response->assertSee('test_security_audit_event');
    }
}
