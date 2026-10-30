<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = Setting::get('app_integration_enabled', 'false');

        if ($enabled !== 'true' && $enabled !== true && $enabled !== '1' && $enabled !== 1) {
            return response()->json([
                'status' => 'error',
                'code' => 'API_INTEGRATION_DISABLED',
                'message' => 'Mobile App API Integration is currently disabled by system administrator. Enable it from Admin Settings.',
            ], 503);
        }

        return $next($request);
    }
}
