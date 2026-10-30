<x-layouts.app>
    <x-slot:title>School Store POS Cash Register</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Store POS Cash Register</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            catalog: {{ Js::from($items) }},
            cart: [],
            selectedStudent: '',
            customerName: '',
            customerType: 'student',
            paymentMethod: 'Cash',
            discount: 0,
            searchCatalog: '',
            selectedCategory: 'All',

            get filteredCatalog() {
                return this.catalog.filter(item => {
                    const matchesCategory = this.selectedCategory === 'All' || item.category === this.selectedCategory;
                    const matchesSearch = !this.searchCatalog || item.name.toLowerCase().includes(this.searchCatalog.toLowerCase()) || item.item_code.toLowerCase().includes(this.searchCatalog.toLowerCase());
                    return matchesCategory && matchesSearch;
                });
            },

            addToCart(item) {
                const existing = this.cart.find(c => c.id === item.id);
                if (existing) {
                    if (existing.quantity < item.stock_quantity) {
                        existing.quantity++;
                    }
                } else {
                    this.cart.push({
                        id: item.id,
                        name: item.name,
                        item_code: item.item_code,
                        price: parseFloat(item.selling_price),
                        stock: item.stock_quantity,
                        quantity: 1
                    });
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            updateQty(index, delta) {
                const item = this.cart[index];
                const newQty = item.quantity + delta;
                if (newQty > 0 && newQty <= item.stock) {
                    item.quantity = newQty;
                }
            },

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            get totalAmount() {
                return Math.max(0, this.subtotal - (parseFloat(this.discount) || 0));
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
                    <a href="{{ route('accounts.store.index') }}" class="hover:text-amber-600">Store Inventory</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">POS Register</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Store Point-of-Sale (POS) Counter
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Fast counter sales for uniforms, books, stationery, with instant stock decrement and PDF memo printing.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                    Cashier: {{ auth()->user()->name }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @if(session('bill_id'))
                    <a
                        href="{{ route('accounts.store.receipt', session('bill_id')) }}"
                        target="_blank"
                        class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Print Bill Memo</span>
                    </a>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2">
                <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main 2-Column POS Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left 7 Cols: Product Catalog Browser -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Search & Category Filters -->
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-3">
                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchCatalog"
                            placeholder="Type to search articles, books, uniform size..."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 py-2.5 pl-9 pr-3 text-slate-900 dark:text-white"
                        />
                        <svg class="h-4 w-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                        <button
                            type="button"
                            @click="selectedCategory = 'All'"
                            :class="selectedCategory === 'All' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                            class="px-3 py-1.5 rounded-lg font-bold cursor-pointer transition-colors"
                        >
                            All
                        </button>
                        <template x-for="cat in ['Uniform', 'Book', 'Notebook', 'Bag', 'Stationery']" :key="cat">
                            <button
                                type="button"
                                @click="selectedCategory = cat"
                                :class="selectedCategory === cat ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                                class="px-3 py-1.5 rounded-lg font-bold cursor-pointer whitespace-nowrap transition-colors"
                                x-text="cat"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <template x-for="item in filteredCatalog" :key="item.id">
                        <div
                            @click="addToCart(item)"
                            class="bg-white dark:bg-slate-800 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs hover:border-amber-500 dark:hover:border-amber-500 cursor-pointer transition-all hover:scale-[1.01] flex flex-col justify-between"
                        >
                            <div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1">
                                    <span x-text="item.item_code"></span>
                                    <span class="px-1.5 py-0.5 rounded font-bold" :class="item.stock_quantity <= item.alert_threshold ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'" x-text="'Stock: ' + item.stock_quantity"></span>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-2" x-text="item.name"></h4>
                            </div>

                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                                <span class="font-mono font-bold text-sm text-emerald-700 dark:text-emerald-400" x-text="'₹ ' + parseFloat(item.selling_price).toFixed(2)"></span>
                                <span class="h-6 w-6 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs">
                                    +
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right 5 Cols: Active POS Cart & Billing Form -->
            <div class="lg:col-span-5">
                <form method="POST" action="{{ route('accounts.store.pos.checkout') }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-5 space-y-4">
                    @csrf

                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <h3 class="font-extrabold font-heading text-sm text-slate-900 dark:text-white">
                                Current Cash Memo Cart
                            </h3>
                        </div>
                        <span class="text-xs font-mono font-bold text-amber-600" x-text="cart.length + ' Item(s)'"></span>
                    </div>

                    <!-- Customer Info -->
                    <div class="space-y-3 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Buyer / Student</label>
                            <select
                                name="student_id"
                                x-model="selectedStudent"
                                class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                            >
                                <option value="">-- General Walk-in Customer --</option>
                                @foreach($classes as $cl)
                                    <optgroup label="{{ $cl->name }}">
                                        @foreach($cl->students as $st)
                                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_no }})</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2" x-show="!selectedStudent">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Customer Name</label>
                                <input
                                    type="text"
                                    name="customer_name"
                                    x-model="customerName"
                                    placeholder="e.g. Ramesh K."
                                    class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                                />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Customer Type</label>
                                <select
                                    name="customer_type"
                                    x-model="customerType"
                                    class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                                >
                                    <option value="student">Student</option>
                                    <option value="parent">Parent</option>
                                    <option value="staff">Staff Member</option>
                                    <option value="visitor">Visitor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Items Table -->
                    <div class="max-h-64 overflow-y-auto space-y-2 border-y border-slate-100 dark:border-slate-700 py-3">
                        <template x-if="cart.length === 0">
                            <div class="py-8 text-center text-xs text-slate-400">
                                Cart is empty. Click items from catalog on the left to add.
                            </div>
                        </template>

                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 dark:bg-slate-700/50 text-xs">
                                <div class="flex-1 mr-2">
                                    <span class="font-bold text-slate-800 dark:text-slate-100 block" x-text="item.name"></span>
                                    <span class="font-mono text-[10px] text-slate-400" x-text="'₹ ' + item.price.toFixed(2) + ' each'"></span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="updateQty(index, -1)"
                                        class="h-6 w-6 rounded bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 font-bold"
                                    >-</button>
                                    <span class="font-mono font-bold w-6 text-center" x-text="item.quantity"></span>
                                    <button
                                        type="button"
                                        @click="updateQty(index, 1)"
                                        class="h-6 w-6 rounded bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 font-bold"
                                    >+</button>
                                </div>

                                <div class="text-right w-20 ml-2">
                                    <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'₹ ' + (item.price * item.quantity).toFixed(2)"></span>
                                </div>

                                <button
                                    type="button"
                                    @click="removeFromCart(index)"
                                    class="text-rose-500 hover:text-rose-700 ml-2"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <!-- Hidden Inputs for submission -->
                                <input type="hidden" :name="'items[' + index + '][store_item_id]'" :value="item.id">
                                <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                                <input type="hidden" :name="'items[' + index + '][unit_price]'" :value="item.price">
                            </div>
                        </template>
                    </div>

                    <!-- Payment Mode & Summary -->
                    <div class="space-y-2 pt-2 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Gross Subtotal</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="'₹ ' + subtotal.toFixed(2)"></span>
                        </div>

                        <div class="flex items-center justify-between text-slate-500">
                            <span>Discount (₹)</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="discount"
                                x-model="discount"
                                class="w-24 text-right text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-1 font-mono font-bold"
                            />
                        </div>

                        <div class="flex items-center justify-between text-sm font-bold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span class="font-heading">Net Total Payable</span>
                            <span class="font-mono text-xl text-emerald-600 dark:text-emerald-400" x-text="'₹ ' + totalAmount.toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Payment Method Select -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Payment Method</label>
                        <select
                            name="payment_method"
                            x-model="paymentMethod"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                        >
                            <option value="Cash">Cash (Counter)</option>
                            <option value="UPI">UPI / Google Pay</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="cart.length === 0"
                        class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Checkout & Print Bill Memo</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
