<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $categories = [
            'Maintenance',
            'Salaries',
            'Utilities',
            'Stationery',
            'Paper & Printing',
            'Laboratory',
            'Sports & Events',
            'IT & Technology',
            'Transport',
            'Miscellaneous',
        ];

        $selectedCategory = $request->get('category');
        $search = $request->get('search');
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $query = Expense::with('approver');

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('bill_no', 'like', "%{$search}%");
            });
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        $expenses = $query->orderByDesc('payment_date')->orderByDesc('id')->paginate(15)->withQueryString();

        $monthTotal = Expense::where('payment_date', '>=', now()->startOfMonth()->toDateString())->sum('amount');
        $todayTotal = Expense::where('payment_date', now()->toDateString())->sum('amount');
        $categoryTotals = Expense::where('payment_date', '>=', now()->startOfMonth()->toDateString())
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return view('accounts.expenses.index', compact(
            'expenses',
            'categories',
            'selectedCategory',
            'fromDate',
            'toDate',
            'monthTotal',
            'todayTotal',
            'categoryTotals'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'vendor_name' => ['nullable', 'string', 'max:150'],
            'bill_no' => ['nullable', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:Cash,Bank Transfer,Cheque,UPI,Card'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expense = Expense::create([
            ...$validated,
            'approved_by' => auth()->id(),
        ]);

        AuditLog::record('expense_voucher_created', $expense, null, [
            'category' => $expense->category,
            'title' => $expense->title,
            'amount' => $expense->amount,
            'vendor' => $expense->vendor_name,
        ]);

        return redirect()->route('accounts.expenses.index')
            ->with('success', "Expense voucher for ₹ " . number_format($expense->amount, 2) . " ({$expense->category}) recorded successfully.");
    }
}
