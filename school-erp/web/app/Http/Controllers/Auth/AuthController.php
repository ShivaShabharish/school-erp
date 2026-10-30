<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'This account has been deactivated. Please contact administration.',
                ]);
            }

            $request->session()->regenerate();

            AuditLog::record('login', $user, null, ['email' => $user->email, 'role' => $user->role]);

            return $this->redirectBasedOnRole($user->role)
                ->with('success', "Welcome back, {$user->name}!");
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::record('logout', Auth::user());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been successfully logged out.');
    }

    protected function redirectBasedOnRole(string $role): RedirectResponse
    {
        return match ($role) {
            'chairman', 'principal' => redirect()->route('admin.dashboard'),
            'accountant' => redirect()->route('accounts.dashboard'),
            'teacher', 'staff' => redirect()->route('staff.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
