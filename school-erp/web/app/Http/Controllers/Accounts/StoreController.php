<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\StoreItem;
use App\Models\StoreSale;
use App\Services\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(
        protected StoreService $storeService
    ) {}

    public function index(Request $request): View
    {
        $categories = ['Uniform', 'Book', 'Notebook', 'Bag', 'Stationery', 'Other'];
        $selectedCategory = $request->get('category');
        $search = $request->get('search');
        $filterLowStock = $request->boolean('low_stock');

        $query = StoreItem::query();

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($filterLowStock) {
            $query->whereColumn('stock_quantity', '<=', 'alert_threshold');
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        $totalItems = StoreItem::count();
        $totalStockValue = StoreItem::selectRaw('sum(stock_quantity * purchase_price) as val')->value('val') ?? 0;
        $lowStockCount = StoreItem::whereColumn('stock_quantity', '<=', 'alert_threshold')->count();
        $monthProfit = StoreSale::where('sale_date', '>=', now()->startOfMonth()->toDateString())->sum('profit_amount');

        return view('accounts.store.index', compact(
            'items',
            'categories',
            'selectedCategory',
            'totalItems',
            'totalStockValue',
            'lowStockCount',
            'monthProfit'
        ));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'item_code' => ['required', 'string', 'max:50', 'unique:store_items,item_code'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'alert_threshold' => ['required', 'integer', 'min:1'],
        ]);

        $item = StoreItem::create($validated);

        AuditLog::record('store_item_created', $item, null, [
            'item_code' => $item->item_code,
            'name' => $item->name,
            'stock' => $item->stock_quantity,
        ]);

        return redirect()->route('accounts.store.index')
            ->with('success', "Item '{$item->name}' added to School Store catalog.");
    }

    public function restock(StoreItem $item, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->storeService->restockItem($item, $validated['quantity'], $validated['purchase_price'] ?? null);

        return redirect()->route('accounts.store.index')
            ->with('success', "Restocked {$validated['quantity']} units of '{$item->name}'. Current stock: {$item->stock_quantity}");
    }

    public function pos(): View
    {
        $items = StoreItem::where('stock_quantity', '>', 0)->orderBy('name')->get();
        $classes = SchoolClass::with(['students' => function ($q) {
            $q->where('status', 'active')->orderBy('first_name');
        }])->orderBy('order_sequence')->get();

        $recentSales = StoreSale::with(['cashier', 'student'])
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('accounts.store.pos', compact('items', 'classes', 'recentSales'));
    }

    public function processSale(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_type' => ['required', 'in:student,staff,parent,visitor'],
            'payment_method' => ['required', 'in:Cash,UPI,Card,Bank Transfer'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.store_item_id' => ['required', 'exists:store_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $sale = $this->storeService->processSale($validated, auth()->user());

            return redirect()->route('accounts.store.pos')
                ->with('success', "Store Sale {$sale->bill_no} completed. Total: ₹ " . number_format($sale->total_amount, 2))
                ->with('bill_id', $sale->id);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function downloadReceipt(StoreSale $sale)
    {
        $pdf = $this->storeService->generateStoreReceiptPdf($sale);
        $safeName = str_replace(['/', '\\'], '-', $sale->bill_no);
        $filename = "Store-Bill-{$safeName}.pdf";

        return $pdf->download($filename);
    }
}
