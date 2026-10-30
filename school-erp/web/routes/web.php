<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Accounts\AccountsDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Students\ParentDashboardController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System Health Route (Rule Requirement)
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    try {
        $dbStatus = DB::connection()->getPdo() ? 'connected' : 'disconnected';
        $dbName = DB::connection()->getDatabaseName();
        $dbOk = true;
    } catch (\Throwable $e) {
        $dbStatus = 'error: ' . $e->getMessage();
        $dbName = null;
        $dbOk = false;
    }

    $statusCode = $dbOk ? 200 : 503;

    return response()->json([
        'status' => $dbOk ? 'ok' : 'degraded',
        'application' => config('app.name'),
        'database' => [
            'status' => $dbStatus,
            'database' => $dbName,
        ],
        'environment' => app()->environment(),
        'php_version' => PHP_VERSION,
        'timestamp' => now()->toIso8601String(),
    ], $statusCode);
})->name('health');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Area (Chairman & Principal)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/metrics/live', [AdminDashboardController::class, 'liveMetrics'])->name('metrics.live');
});

/*
|--------------------------------------------------------------------------
| Accounts & Billing Portal (Chairman, Principal & Accountant)
| Top-Right Dedicated Entry Point
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:chairman,principal,accountant'])->prefix('accounts')->name('accounts.')->group(function () {
    Route::get('/dashboard', [AccountsDashboardController::class, 'index'])->name('dashboard');
    Route::get('/metrics/live', [AccountsDashboardController::class, 'liveBillingMetrics'])->name('metrics.live');
});

/*
|--------------------------------------------------------------------------
| Staff & Teacher Portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:chairman,principal,teacher,staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Parent Portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
});
