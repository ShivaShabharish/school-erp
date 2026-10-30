<?php

use App\Http\Controllers\Api\V1\BiometricApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'School ERP API v1 is active',
            'school' => 'Test Subject Higher Secondary School',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Phase 4: Biometric Punch API
    Route::post('/biometric/punch', [BiometricApiController::class, 'punch'])->name('api.v1.biometric.punch');
});
