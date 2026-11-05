<?php

use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BiometricApiController;
use App\Http\Controllers\Api\V1\StudentPortalController;
use App\Http\Middleware\EnsureApiEnabled;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Health & Status
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'School ERP API v1 is active',
            'school' => 'Test Subject Higher Secondary School',
            'app_integration_enabled' => \App\Models\Setting::get('app_integration_enabled', 'false') === 'true',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Phase 4: Biometric Machine Punch Interface (Always open for hardware terminal)
    Route::post('/biometric/punch', [BiometricApiController::class, 'punch'])->name('api.v1.biometric.punch');

    // AI Campus Assistant (Voice & Chat Co-Pilot)
    Route::post('/ai/assistant', [\App\Http\Controllers\Api\V1\AiAssistantController::class, 'query'])->name('api.v1.ai.assistant');

    // Live Bus GPS Fleet Tracker API (Available for Web & Mobile Co-Pilot)
    Route::get('/transport/bus-tracker/live', [\App\Http\Controllers\Admin\BusTrackerController::class, 'liveFleetData'])->name('api.v1.transport.bus-tracker.live');

    // Mobile App REST API Gateway (Guarded by EnsureApiEnabled switch)
    Route::middleware([EnsureApiEnabled::class])->group(function () {
        // Auth
        Route::post('/auth/token', [AuthController::class, 'login'])->name('api.v1.auth.token');

        // Authenticated Endpoints via Sanctum Bearer Token
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/auth/user', [AuthController::class, 'me'])->name('api.v1.auth.user');
            Route::post('/auth/revoke', [AuthController::class, 'logout'])->name('api.v1.auth.revoke');

            // Student & Parent Portal Endpoints
            Route::get('/student/profile', [StudentPortalController::class, 'profile'])->name('api.v1.student.profile');
            Route::get('/student/attendance', [StudentPortalController::class, 'attendance'])->name('api.v1.student.attendance');
            Route::get('/student/fees', [StudentPortalController::class, 'fees'])->name('api.v1.student.fees');
            Route::get('/student/timetable', [StudentPortalController::class, 'timetable'])->name('api.v1.student.timetable');
            Route::get('/student/homework', [StudentPortalController::class, 'homework'])->name('api.v1.student.homework');

            // Circulars & Notices
            Route::get('/announcements', [AnnouncementController::class, 'index'])->name('api.v1.announcements.index');
        });
    });
});
