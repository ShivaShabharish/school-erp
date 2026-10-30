<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $currentYear = AcademicYear::current();
        $totalStaff = User::whereIn('role', ['teacher', 'staff'])->count();
        $totalParents = User::where('role', 'parent')->count();
        $classesCount = SchoolClass::count();

        // Sample metric foundations ready for Phase 2 live data
        $metrics = [
            'total_students' => 312,
            'students_present' => 298,
            'students_attendance_pct' => 95.5,
            'total_staff' => $totalStaff ?: 24,
            'staff_present' => 23,
            'staff_on_leave' => 1,
            'today_fee_collection' => 48500,
            'month_collection' => 1425000,
            'month_expenses' => 782000,
            'low_stock_items' => 3,
            'fee_defaulters_count' => 14,
        ];

        return view('admin.dashboard', compact('metrics', 'currentYear', 'classesCount'));
    }

    /**
     * Polling endpoint queried every 10s by Alpine.js for live metrics
     */
    public function liveMetrics(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'timestamp' => now()->format('h:i:s A'),
            'metrics' => [
                'students_present' => 298,
                'students_attendance_pct' => 95.5,
                'staff_present' => 23,
                'staff_on_leave' => 1,
                'today_fee_collection' => 48500,
            ],
        ]);
    }
}
