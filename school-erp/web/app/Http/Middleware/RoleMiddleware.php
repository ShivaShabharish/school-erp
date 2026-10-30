<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated. Please contact administration.']);
        }

        // Chairman and Principal have identical top-level administrative access across the ERP
        if (in_array($user->role, ['chairman', 'principal']) && (in_array('admin', $roles) || in_array('chairman', $roles) || in_array('principal', $roles))) {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized action for role: ' . $user->role], 403);
        }

        abort(403, 'Access denied. You do not possess the required permissions to access this portal.');
    }
}
