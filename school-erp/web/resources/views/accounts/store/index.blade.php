<x-layouts.app>
    <x-slot:title>School Store Inventory & Articles</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; School Store Catalog</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newItemModal: false,
            restockModal: false,
            selectedItem: null,
            openRestock(item) {
                this.selectedItem = item;
                this.restockModal = true;
            }
        }"
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('accounts.dashboard') }}" class="hover:text-amber-600 dark:hover:text-amber-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Finance Dashboard
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">School Store</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    School Store Inventory & Stock Catalog
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Uniforms, textbooks, notebooks, school bags, stationery, stock alerts, and profit margins.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('accounts.store.pos') }}"
                    class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Open Store POS Register</span>
                </a>

                <button
                    type="button"
                    @click="newItemModal = true"
                    class="px-3.5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add New Article</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Catalog SKUs</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    {{ $totalItems }} Articles
                </div>
                <p class="mt-1 text-xs text-slate-500">Uniforms, books, bags & accessories</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Stock Asset Value</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    ₹ {{ number_format($totalStockValue, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">At wholesale cost price</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Low Stock Alerts</span>
                <div class="mt-2 text-2xl font-bold font-heading {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $lowStockCount }} Items
                </div>
                <p class="mt-1 text-xs text-slate-500">Below threshold cutoff</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Month Store Margin</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    ₹ {{ number_format($monthProfit, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Gross profit from POS sales</p>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto">
                <a
                    href="{{ route('accounts.store.index') }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ !$selectedCategory && !request('low_stock') ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a
                        href="{{ route('accounts.store.index', ['category' => $cat]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $selectedCategory === $cat ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        {{ $cat }}
                    </a>
                @endforeach
                <a
                    href="{{ route('accounts.store.index', ['low_stock' => 1]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ request('low_stock') ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}"
                >
                    Low Stock Only ({{ $lowStockCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('accounts.store.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search item code or name..."
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                />
                <button
                    type="submit"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold"
                >
                    Filter
                </button>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Item Code</th>
                            <th class="px-4 py-3">Article Description</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3 text-right">Cost Price (₹)</th>
                            <th class="px-4 py-3 text-right">Selling Price (₹)</th>
                            <th class="px-4 py-3 text-right">Margin / Profit</th>
                            <th class="px-4 py-3 text-center">In Stock</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono font-bold text-amber-700 dark:text-amber-400">
                                    {{ $item->item_code }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $item->name }}</span>
                                    <span class="text-[10px] text-slate-400">Threshold: {{ $item->alert_threshold }} units</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-slate-600 dark:text-slate-300">
                                    {{ number_format($item->purchase_price, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900 dark:text-white text-sm">
                                    {{ number_format($item->selling_price, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-emerald-600 font-bold">
                                    +₹ {{ number_format($item->selling_price - $item->purchase_price, 2) }}
                                    <span class="text-[10px] text-slate-400 block font-normal">
                                        ({{ $item->purchase_price > 0 ? round((($item->selling_price - $item->purchase_price) / $item->purchase_price) * 100, 0) : 0 }}%)
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-black {{ $item->isLowStock() ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 animate-pulse' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                        {{ $item->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <button
                                        type="button"
                                        @click="openRestock({{ Js::from($item) }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold cursor-pointer"
                                    >
                                        Restock
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                    No items match the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        <!-- Add New Item Modal -->
        <div
            x-show="newItemModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newItemModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Add New Store Article
                    </h3>
                    <button @click="newItemModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounts.store.items.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Article Name *</label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="e.g. Boys Uniform Shirt (Size 34), Class 10 Maths Textbook"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Item Code / SKU *</label>
                            <input
                                type="text"
                                name="item_code"
                                required
                                placeholder="e.g. UNI-SHIRT-34"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category *</label>
                            <select
                                name="category"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="Uniform">Uniform</option>
                                <option value="Book">Textbook</option>
                                <option value="Notebook">Notebook</option>
                                <option value="Bag">School Bag</option>
                                <option value="Stationery">Stationery</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cost / Purchase Price (₹) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="purchase_price"
                                required
                                placeholder="e.g. 250"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Selling Price (₹) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="selling_price"
                                required
                                placeholder="e.g. 380"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Initial Stock Units *</label>
                            <input
                                type="number"
                                min="0"
                                name="stock_quantity"
                                required
                                value="50"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Low Stock Alert Cutoff *</label>
                            <input
                                type="number"
                                min="1"
                                name="alert_threshold"
                                required
                                value="10"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newItemModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs cursor-pointer"
                        >
                            Save Item to Store
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Restock Item Modal -->
        <div
            x-show="restockModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="restockModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Restock Inventory Article
                    </h3>
                    <button @click="restockModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form
                    method="POST"
                    :action="selectedItem ? '/accounts/store/items/' + selectedItem.id + '/restock' : '#'"
                    class="space-y-4 mt-4"
                >
                    @csrf
                    <div>
                        <span class="text-xs text-slate-500 block mb-0.5">Article</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm" x-text="selectedItem?.name"></span>
                        <span class="text-xs font-mono text-amber-600 block" x-text="'Current Stock: ' + (selectedItem?.stock_quantity || 0) + ' units'"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Additional Quantity to Add *</label>
                        <input
                            type="number"
                            name="quantity"
                            required
                            min="1"
                            value="25"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Revised Purchase Cost (Optional)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="purchase_price"
                            placeholder="Leave empty to retain existing cost price"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                        />
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="restockModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Confirm Restock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
