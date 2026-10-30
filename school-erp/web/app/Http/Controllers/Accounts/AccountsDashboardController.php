<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AccountsDashboardController extends Controller
{
    public function index(): View
    {
        $currentYear = AcademicYear::current();

        $metrics = [
            'total_receivables' => 4500000,
            'collected_to_date' => 3850000,
            'pending_dues' => 650000,
            'today_collections' => 48500,
            'month_expenses' => 782000,
            'net_cash_flow' => 643000,
            'store_sales_month' => 96400,
        ];

        return view('accounts.dashboard', compact('metrics', 'currentYear'));
    }

    public function liveBillingMetrics(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'timestamp' => now()->format('h:i:s A'),
            'today_collections' => 48500,
            'pending_dues' => 650000,
        ]);
    }
}
