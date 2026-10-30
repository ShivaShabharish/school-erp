<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_endpoint_returns_ok_and_connected_db(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'application' => config('app.name'),
            'database' => [
                'status' => 'connected',
                'database' => 'school_erp',
            ],
        ]);
    }
}
