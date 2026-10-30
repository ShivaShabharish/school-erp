<?php

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
});
