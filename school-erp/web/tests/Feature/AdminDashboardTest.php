<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_chairman_can_view_admin_dashboard_with_all_metrics(): void
    {
        $chairman = User::where('email', 'chairman@school.test')->first();

        $response = $this->actingAs($chairman)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Institution Operations Dashboard');
        $response->assertSee('Students Present');
        $response->assertSee('Staff on Duty');
        $response->assertSee('7-Day Attendance Trend');
        $response->assertSee('Operational Alerts');
        $response->assertSee('Late Staff Arrivals');
        $response->assertSee('Accounts & Billing', false);
    }

    public function test_principal_can_view_admin_dashboard(): void
    {
        $principal = User::where('email', 'principal@school.test')->first();

        $response = $this->actingAs($principal)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Institution Operations Dashboard');
    }

    public function test_accountant_cannot_access_admin_dashboard(): void
    {
        $accountant = User::where('email', 'accountant@school.test')->first();

        $response = $this->actingAs($accountant)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_live_metrics_endpoint_returns_json_payload(): void
    {
        $chairman = User::where('email', 'chairman@school.test')->first();

        $response = $this->actingAs($chairman)->get(route('admin.metrics.live'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'metrics' => [
                'students_present',
                'students_attendance_pct',
                'staff_present',
                'staff_on_leave',
                'today_fee_collection',
                'net_month_margin',
                'total_alerts',
            ],
        ]);
        $response->assertJson(['status' => 'success']);
    }
}
