<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FeeReceipt;
use App\Models\StoreSale;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function transactions(Request $request): View
    {
        $type = $request->get('type', 'all'); // all, fee, store, expense
        $search = $request->get('search');
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $transactions = $this->fetchTransactions($type, $search, $fromDate, $toDate);

        $totalCredits = $transactions->where('flow', 'credit')->sum('amount');
        $totalDebits = $transactions->where('flow', 'debit')->sum('amount');
        $netBalance = $totalCredits - $totalDebits;

        return view('accounts.reports.transactions', compact(
            'transactions',
            'type',
            'search',
            'fromDate',
            'toDate',
            'totalCredits',
            'totalDebits',
            'netBalance'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $transactions = $this->fetchTransactions($type, $search, $fromDate, $toDate);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Financial_Transactions_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Date',
                'Reference / Bill No',
                'Type',
                'Category',
                'Party / Beneficiary',
                'Flow',
                'Payment Method',
                'Amount (INR)',
                'Notes',
            ]);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t['date'],
                    $t['reference'],
                    $t['type_label'],
                    $t['category'],
                    $t['party'],
                    strtoupper($t['flow']),
                    $t['payment_method'],
                    number_format($t['amount'], 2, '.', ''),
                    $t['notes'],
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function daybook(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());
        $daybook = $this->reportService->getDayBook($date);

        return view('accounts.reports.daybook', compact('daybook', 'date'));
    }

    public function monthly(Request $request): View
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $feeIncome = FeeReceipt::whereBetween('receipt_date', [$startDate, $endDate])->sum('amount_paid');
        $storeIncome = StoreSale::whereBetween('sale_date', [$startDate, $endDate])->sum('total_amount');
        $storeProfit = StoreSale::whereBetween('sale_date', [$startDate, $endDate])->sum('profit_amount');
        $totalIncome = $feeIncome + $storeIncome;

        $expenses = Expense::whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalExpense = $expenses->sum('total');
        $netSurplus = $totalIncome - $totalExpense;
        $operatingMargin = $totalIncome > 0 ? round(($netSurplus / $totalIncome) * 100, 1) : 0;

        return view('accounts.reports.monthly', compact(
            'year',
            'month',
            'feeIncome',
            'storeIncome',
            'storeProfit',
            'totalIncome',
            'expenses',
            'totalExpense',
            'netSurplus',
            'operatingMargin'
        ));
    }

    protected function fetchTransactions(string $type, ?string $search, ?string $fromDate, ?string $toDate)
    {
        $records = collect();

        // 1. Fee Receipts
        if ($type === 'all' || $type === 'fee') {
            $feeQuery = FeeReceipt::with('student')
                ->when($fromDate && $toDate, fn($q) => $q->whereBetween('receipt_date', [$fromDate, $toDate]))
                ->when($search, function ($q) use ($search) {
                    $q->where('receipt_no', 'like', "%{$search}%")
                      ->orWhereHas('student', fn($sq) => $sq->where('first_name', 'like', "%{$search}%"));
                })
                ->get()
                ->map(fn($r) => [
                    'date' => $r->receipt_date->format('Y-m-d'),
                    'reference' => $r->receipt_no,
                    'type' => 'fee',
                    'type_label' => 'Fee Receipt',
                    'category' => 'Tuition & Fees',
                    'party' => $r->student?->full_name ?? 'Student',
                    'flow' => 'credit',
                    'payment_method' => $r->payment_method,
                    'amount' => (float) $r->amount_paid,
                    'notes' => $r->notes,
                    'created_at' => $r->created_at,
                ]);
            $records = $records->concat($feeQuery);
        }

        // 2. Store Sales
        if ($type === 'all' || $type === 'store') {
            $storeQuery = StoreSale::when($fromDate && $toDate, fn($q) => $q->whereBetween('sale_date', [$fromDate, $toDate]))
                ->when($search, function ($q) use ($search) {
                    $q->where('bill_no', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                })
                ->get()
                ->map(fn($s) => [
                    'date' => $s->sale_date->format('Y-m-d'),
                    'reference' => $s->bill_no,
                    'type' => 'store',
                    'type_label' => 'Store POS',
                    'category' => 'Store Sales',
                    'party' => $s->customer_name,
                    'flow' => 'credit',
                    'payment_method' => $s->payment_method,
                    'amount' => (float) $s->total_amount,
                    'notes' => $s->notes,
                    'created_at' => $s->created_at,
                ]);
            $records = $records->concat($storeQuery);
        }

        // 3. Expenses
        if ($type === 'all' || $type === 'expense') {
            $expQuery = Expense::when($fromDate && $toDate, fn($q) => $q->whereBetween('payment_date', [$fromDate, $toDate]))
                ->when($search, function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('vendor_name', 'like', "%{$search}%")
                      ->orWhere('bill_no', 'like', "%{$search}%");
                })
                ->get()
                ->map(fn($e) => [
                    'date' => $e->payment_date->format('Y-m-d'),
                    'reference' => $e->bill_no ?: 'EXP-' . $e->id,
                    'type' => 'expense',
                    'type_label' => 'Expense Voucher',
                    'category' => $e->category,
                    'party' => $e->vendor_name ?: 'Vendor',
                    'flow' => 'debit',
                    'payment_method' => $e->payment_method,
                    'amount' => (float) $e->amount,
                    'notes' => $e->notes,
                    'created_at' => $e->created_at,
                ]);
            $records = $records->concat($expQuery);
        }

        return $records->sortByDesc('created_at')->values();
    }
}
