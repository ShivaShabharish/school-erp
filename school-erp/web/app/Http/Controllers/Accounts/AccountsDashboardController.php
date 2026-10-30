<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\StoreItem;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AccountsDashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(): View
    {
        $currentYear = AcademicYear::current();
        $metrics = $this->reportService->getAccountsOverviewMetrics();

        $classes = SchoolClass::with('sections')->orderBy('order_sequence')->get();
        $feeStructures = FeeStructure::with('schoolClass')
            ->where('academic_year_id', $currentYear?->id)
            ->get();

        $lowStockItems = StoreItem::whereColumn('stock_quantity', '<=', 'alert_threshold')
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        return view('accounts.dashboard', compact('metrics', 'currentYear', 'classes', 'feeStructures', 'lowStockItems'));
    }

    public function liveBillingMetrics(): JsonResponse
    {
        $metrics = $this->reportService->getAccountsOverviewMetrics();

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->format('h:i:s A'),
            'today_collections' => $metrics['today_collections'],
            'pending_dues' => $metrics['pending_dues'],
            'month_income' => $metrics['month_income'],
            'month_expenses' => $metrics['month_expenses'],
            'net_cash_flow' => $metrics['net_cash_flow'],
        ]);
    }
}
