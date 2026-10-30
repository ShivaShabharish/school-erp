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

    // Phase 3: Students & Admissions Routes
    Route::get('/students/export-csv', [\App\Http\Controllers\Students\StudentController::class, 'exportCsv'])->name('students.export-csv');
    Route::get('/students/sample-csv', [\App\Http\Controllers\Students\StudentController::class, 'downloadSampleCsv'])->name('students.sample-csv');
    Route::post('/students/import-csv', [\App\Http\Controllers\Students\StudentController::class, 'importCsv'])->name('students.import-csv');
    Route::get('/students/promotion', [\App\Http\Controllers\Students\PromotionController::class, 'index'])->name('students.promotion');
    Route::post('/students/promotion', [\App\Http\Controllers\Students\PromotionController::class, 'promote'])->name('students.promote');
    Route::post('/students/promotion/execute', [\App\Http\Controllers\Students\PromotionController::class, 'promote'])->name('students.promotion.promote');

    Route::resource('students', \App\Http\Controllers\Students\StudentController::class);

    Route::get('/admissions/enquiries', [\App\Http\Controllers\Students\AdmissionController::class, 'enquiries'])->name('admissions.enquiries');
    Route::post('/admissions/enquiries', [\App\Http\Controllers\Students\AdmissionController::class, 'storeEnquiry'])->name('admissions.store-enquiry');
    Route::post('/admissions/enquiry/store', [\App\Http\Controllers\Students\AdmissionController::class, 'storeEnquiry'])->name('admissions.enquiry.store');
    Route::post('/admissions/enquiries/{enquiry}/convert', [\App\Http\Controllers\Students\AdmissionController::class, 'convertEnquiry'])->name('admissions.convert-enquiry');
    Route::post('/admissions/enquiry/{enquiry}/convert', [\App\Http\Controllers\Students\AdmissionController::class, 'convertEnquiry'])->name('admissions.enquiry.convert');

    Route::get('/classes', [\App\Http\Controllers\Students\ClassDetailController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}/sections/{section}', [\App\Http\Controllers\Students\ClassDetailController::class, 'show'])->name('classes.show');

    // Phase 4: Staff, Attendance, Leaves & Payroll
    Route::get('/staff/biometric-simulator', [\App\Http\Controllers\Staff\AttendanceController::class, 'biometricSimulator'])->name('staff.biometric-simulator');
    Route::post('/staff/biometric-simulator/punch', [\App\Http\Controllers\Staff\AttendanceController::class, 'simulatePunch'])->name('staff.simulate-punch');
    Route::get('/staff/attendance', [\App\Http\Controllers\Staff\AttendanceController::class, 'index'])->name('staff.attendance');

    Route::get('/staff/leaves', [\App\Http\Controllers\Staff\LeaveController::class, 'index'])->name('staff.leaves.index');
    Route::post('/staff/leaves', [\App\Http\Controllers\Staff\LeaveController::class, 'apply'])->name('staff.leaves.apply');
    Route::post('/staff/leaves/{leaveApplication}/approve', [\App\Http\Controllers\Staff\LeaveController::class, 'approve'])->name('staff.leaves.approve');
    Route::post('/staff/leaves/{leaveApplication}/reject', [\App\Http\Controllers\Staff\LeaveController::class, 'reject'])->name('staff.leaves.reject');

    Route::get('/staff/payroll', [\App\Http\Controllers\Staff\PayrollController::class, 'index'])->name('staff.payroll.index');
    Route::get('/staff/payroll/{payroll}/payslip', [\App\Http\Controllers\Staff\PayrollController::class, 'downloadPayslip'])->name('staff.payroll.payslip');

    Route::resource('staff', \App\Http\Controllers\Staff\StaffController::class);

    // Phase 5: Academics & Timetables
    Route::get('/academics/timetables', [\App\Http\Controllers\Academics\TimetableController::class, 'index'])->name('academics.timetables.index');
    Route::post('/academics/timetables', [\App\Http\Controllers\Academics\TimetableController::class, 'store'])->name('academics.timetables.store');

    Route::get('/academics/substitutions', [\App\Http\Controllers\Academics\SubstitutionController::class, 'index'])->name('academics.substitutions.index');
    Route::post('/academics/substitutions', [\App\Http\Controllers\Academics\SubstitutionController::class, 'assign'])->name('academics.substitutions.assign');

    Route::get('/academics/exams', [\App\Http\Controllers\Academics\ExamController::class, 'index'])->name('academics.exams.index');
    Route::post('/academics/exams', [\App\Http\Controllers\Academics\ExamController::class, 'store'])->name('academics.exams.store');
    Route::get('/academics/exams/{exam}/students/{student}/report-card', [\App\Http\Controllers\Academics\ExamController::class, 'reportCardPdf'])->name('academics.exams.report-card');
    Route::get('/academics/exams/schedules/{schedule}/marks', [\App\Http\Controllers\Academics\MarksController::class, 'index'])->name('academics.exams.marks.index');
    Route::post('/academics/exams/schedules/{schedule}/marks', [\App\Http\Controllers\Academics\MarksController::class, 'saveMarks'])->name('academics.exams.marks.save');

    Route::get('/academics/homework', [\App\Http\Controllers\Academics\HomeworkController::class, 'index'])->name('academics.homework.index');
    Route::post('/academics/homework', [\App\Http\Controllers\Academics\HomeworkController::class, 'store'])->name('academics.homework.store');

    Route::get('/academics/student-attendance', [\App\Http\Controllers\Academics\StudentAttendanceController::class, 'index'])->name('academics.student-attendance.index');
    Route::post('/academics/student-attendance', [\App\Http\Controllers\Academics\StudentAttendanceController::class, 'record'])->name('academics.student-attendance.record');
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

    // Fee Structures & Class Installments
    Route::get('/fees', [\App\Http\Controllers\Accounts\FeeStructureController::class, 'index'])->name('fees.index');
    Route::post('/fees', [\App\Http\Controllers\Accounts\FeeStructureController::class, 'store'])->name('fees.store');

    // Cashier Collection Desk & Dues
    Route::get('/fees/collect', [\App\Http\Controllers\Accounts\FeeCollectionController::class, 'collect'])->name('fees.collect');
    Route::post('/fees/collect', [\App\Http\Controllers\Accounts\FeeCollectionController::class, 'store'])->name('fees.collect.store');
    Route::get('/fees/receipts/{receipt}/pdf', [\App\Http\Controllers\Accounts\FeeCollectionController::class, 'downloadReceipt'])->name('fees.receipt');
    Route::get('/fees/student/{student}/dues-json', [\App\Http\Controllers\Accounts\FeeCollectionController::class, 'studentDuesJson'])->name('fees.student-dues');

    // Concessions & Scholarships
    Route::get('/fees/concessions', [\App\Http\Controllers\Accounts\FeeConcessionController::class, 'index'])->name('fees.concessions');
    Route::post('/fees/concessions', [\App\Http\Controllers\Accounts\FeeConcessionController::class, 'store'])->name('fees.concessions.store');

    // School Store POS & Inventory
    Route::get('/store', [\App\Http\Controllers\Accounts\StoreController::class, 'index'])->name('store.index');
    Route::post('/store/items', [\App\Http\Controllers\Accounts\StoreController::class, 'storeItem'])->name('store.items.store');
    Route::post('/store/items/{item}/restock', [\App\Http\Controllers\Accounts\StoreController::class, 'restock'])->name('store.items.restock');
    Route::get('/store/pos', [\App\Http\Controllers\Accounts\StoreController::class, 'pos'])->name('store.pos');
    Route::post('/store/pos/checkout', [\App\Http\Controllers\Accounts\StoreController::class, 'processSale'])->name('store.pos.checkout');
    Route::get('/store/sales/{sale}/pdf', [\App\Http\Controllers\Accounts\StoreController::class, 'downloadReceipt'])->name('store.receipt');

    // Double-Entry Expense Ledger
    Route::get('/expenses', [\App\Http\Controllers\Accounts\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [\App\Http\Controllers\Accounts\ExpenseController::class, 'store'])->name('expenses.store');

    // Reports, Day-Book & Audits
    Route::get('/reports/transactions', [\App\Http\Controllers\Accounts\ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('/reports/transactions/export', [\App\Http\Controllers\Accounts\ReportController::class, 'exportCsv'])->name('reports.transactions.export');
    Route::get('/reports/daybook', [\App\Http\Controllers\Accounts\ReportController::class, 'daybook'])->name('reports.daybook');
    Route::get('/reports/monthly', [\App\Http\Controllers\Accounts\ReportController::class, 'monthly'])->name('reports.monthly');
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
