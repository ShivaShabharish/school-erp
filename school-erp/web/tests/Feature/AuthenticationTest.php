<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Test Subject Higher Secondary School');
    }

    public function test_chairman_can_authenticate_and_redirects_to_admin_dashboard(): void
    {
        $user = User::where('email', 'chairman@school.test')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_accountant_can_authenticate_and_redirects_to_accounts_dashboard(): void
    {
        $user = User::where('email', 'accountant@school.test')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('accounts.dashboard'));
    }

    public function test_teacher_can_authenticate_and_redirects_to_staff_dashboard(): void
    {
        $user = User::where('email', 'teacher@school.test')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('staff.dashboard'));
    }

    public function test_parent_can_authenticate_and_redirects_to_parent_dashboard(): void
    {
        $user = User::where('email', 'parent@school.test')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('parent.dashboard'));
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_parent_cannot_access_admin_dashboard(): void
    {
        $parent = User::where('email', 'parent@school.test')->first();

        $response = $this->actingAs($parent)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'principal@school.test')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
