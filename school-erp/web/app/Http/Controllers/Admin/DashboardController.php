<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(): View
    {
        $currentYear = AcademicYear::current();
        $classesCount = SchoolClass::count();

        $metrics = $this->reportService->getDashboardMetrics();
        $classWiseData = $this->reportService->getClassWiseAttendance();
        $trendData = $this->reportService->getAttendanceTrend();
        $alerts = $this->reportService->getOperationalAlerts();

        return view('admin.dashboard', compact(
            'metrics',
            'currentYear',
            'classesCount',
            'classWiseData',
            'trendData',
            'alerts'
        ));
    }

    /**
     * Polling endpoint queried every 10s by Alpine.js for live metrics
     */
    public function liveMetrics(): JsonResponse
    {
        $metrics = $this->reportService->getDashboardMetrics();
        $alerts = $this->reportService->getOperationalAlerts();

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->format('h:i:s A'),
            'metrics' => [
                'students_present' => $metrics['students_present'],
                'students_attendance_pct' => $metrics['students_attendance_pct'],
                'staff_present' => $metrics['staff_present'],
                'staff_on_leave' => $metrics['staff_on_leave'],
                'today_fee_collection' => $metrics['today_fee_collection'],
                'net_month_margin' => $metrics['net_month_margin'],
                'total_alerts' => $alerts['total_alerts'],
            ],
        ]);
    }
}
