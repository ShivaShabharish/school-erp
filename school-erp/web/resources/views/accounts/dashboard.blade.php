<x-layouts.app>
    <x-slot:title>Accounts & Billing Portal</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing Portal</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            todayCollections: {{ $metrics['today_collections'] }},
            pendingDues: {{ $metrics['pending_dues'] }},
            activeTab: 'overview',
            feeModalOpen: false,
            expenseModalOpen: false,
            lastPoll: '{{ now()->format('h:i:s A') }}',
            fetchLiveBilling() {
                fetch('{{ route('accounts.metrics.live') }}')
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.todayCollections = data.today_collections;
                            this.pendingDues = data.pending_dues;
                            this.lastPoll = data.timestamp;
                        }
                    });
            }
        }"
        x-init="
            setInterval(function() { fetchLiveBilling(); }, 10000);
        "
    >
        <!-- Accounts Portal Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 rounded-2xl shadow-md">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black tracking-widest bg-amber-950/40 uppercase">Dedicated Finance Division</span>
                    <span class="text-xs text-amber-100">&bull; Academic Year: {{ $currentYear?->name ?? '2026-2027' }}</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading tracking-tight mt-1">
                    Accounts & Billing Management
                </h1>
                <p class="text-xs text-amber-100 mt-1 max-w-xl">
                    Fee collections, class installment schedules, concessions, double-entry expense ledger, and School Store inventory POS.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <button
                    type="button"
                    @click="feeModalOpen = true"
                    class="px-4 py-2 rounded-xl bg-white hover:bg-amber-50 text-amber-800 font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2 transition-transform active:scale-95"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Collect Fee</span>
                </button>

                <button
                    type="button"
                    @click="expenseModalOpen = true"
                    class="px-4 py-2 rounded-xl bg-amber-800/80 hover:bg-amber-800 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2 border border-amber-500/50"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Record Expense</span>
                </button>
            </div>
        </div>

        <!-- 4 Key Financial Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">
                    <span>Today's Collections</span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded font-bold">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> LIVE
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="'₹ ' + Number(todayCollections).toLocaleString('en-IN')">
                    ₹ {{ number_format($metrics['today_collections']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cash, UPI & Bank entries today</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">
                    <span>Total Collected to Date</span>
                    <span class="text-xs font-bold text-emerald-600">85.5%</span>
                </div>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-700 dark:text-emerald-400">
                    ₹ {{ number_format($metrics['collected_to_date']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total Year Budget: ₹ {{ number_format($metrics['total_receivables']) }}</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">
                    <span>Pending Fee Dues</span>
                    <span class="text-xs font-bold text-rose-600">14.5%</span>
                </div>
                <div class="mt-2 text-2xl font-bold font-heading text-rose-600 dark:text-rose-400" x-text="'₹ ' + Number(pendingDues).toLocaleString('en-IN')">
                    ₹ {{ number_format($metrics['pending_dues']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Includes Term 1 & Term 2 arrears</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">
                    <span>Store POS Revenue</span>
                    <span class="text-xs font-bold text-amber-600">Month</span>
                </div>
                <div class="mt-2 text-2xl font-bold font-heading text-amber-600 dark:text-amber-400">
                    ₹ {{ number_format($metrics['store_sales_month']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Uniforms, books & stationery</p>
            </div>
        </div>

        <!-- Section: Fee Structure & Store Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 3-Term Fee Schedule Per Class -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div>
                        <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">Fee Schedule (3 Terms)</h3>
                        <p class="text-xs text-slate-500">Term 1 (June), Term 2 (Oct), Term 3 (Jan)</p>
                    </div>
                    <x-badge variant="neutral">2026-27 Approved</x-badge>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-3 py-2">Class Group</th>
                                <th class="px-3 py-2">Term 1</th>
                                <th class="px-3 py-2">Term 2</th>
                                <th class="px-3 py-2">Term 3</th>
                                <th class="px-3 py-2 text-right">Annual Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Kindergarten (LKG & UKG)</td>
                                <td class="px-3 py-2.5">₹ 8,000</td>
                                <td class="px-3 py-2.5">₹ 6,000</td>
                                <td class="px-3 py-2.5">₹ 6,000</td>
                                <td class="px-3 py-2.5 font-bold text-right text-emerald-600">₹ 20,000</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Primary (Classes 1 - 5)</td>
                                <td class="px-3 py-2.5">₹ 10,000</td>
                                <td class="px-3 py-2.5">₹ 8,000</td>
                                <td class="px-3 py-2.5">₹ 8,000</td>
                                <td class="px-3 py-2.5 font-bold text-right text-emerald-600">₹ 26,000</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Middle School (Classes 6 - 8)</td>
                                <td class="px-3 py-2.5">₹ 12,000</td>
                                <td class="px-3 py-2.5">₹ 10,000</td>
                                <td class="px-3 py-2.5">₹ 10,000</td>
                                <td class="px-3 py-2.5 font-bold text-right text-emerald-600">₹ 32,000</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Secondary (Classes 9 - 10)</td>
                                <td class="px-3 py-2.5">₹ 15,000</td>
                                <td class="px-3 py-2.5">₹ 12,000</td>
                                <td class="px-3 py-2.5">₹ 12,000</td>
                                <td class="px-3 py-2.5 font-bold text-right text-emerald-600">₹ 39,000</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Higher Sec (Classes 11 - 12)</td>
                                <td class="px-3 py-2.5">₹ 18,000</td>
                                <td class="px-3 py-2.5">₹ 15,000</td>
                                <td class="px-3 py-2.5">₹ 15,000</td>
                                <td class="px-3 py-2.5 font-bold text-right text-emerald-600">₹ 48,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- School Store Inventory Quick View -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div>
                        <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">School Store & Uniform Inventory</h3>
                        <p class="text-xs text-slate-500">Live stock levels & low stock alerts</p>
                    </div>
                    <x-badge variant="warning">3 Items Low</x-badge>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-3 py-2">Item Name</th>
                                <th class="px-3 py-2">Category</th>
                                <th class="px-3 py-2">Stock</th>
                                <th class="px-3 py-2">Price</th>
                                <th class="px-3 py-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Regular School Uniform (Size 32)</td>
                                <td class="px-3 py-2.5 text-slate-500">Uniform</td>
                                <td class="px-3 py-2.5 font-bold text-rose-600">4 left</td>
                                <td class="px-3 py-2.5">₹ 850</td>
                                <td class="px-3 py-2.5 text-right"><x-badge variant="danger">Low Stock</x-badge></td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">Mathematics 192-Pg Long Notebook</td>
                                <td class="px-3 py-2.5 text-slate-500">Notebook</td>
                                <td class="px-3 py-2.5 font-bold text-emerald-600">142</td>
                                <td class="px-3 py-2.5">₹ 65</td>
                                <td class="px-3 py-2.5 text-right"><x-badge variant="success">In Stock</x-badge></td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">State Board Tamil Reader Class 10</td>
                                <td class="px-3 py-2.5 text-slate-500">Textbook</td>
                                <td class="px-3 py-2.5 font-bold text-emerald-600">88</td>
                                <td class="px-3 py-2.5">₹ 140</td>
                                <td class="px-3 py-2.5 text-right"><x-badge variant="success">In Stock</x-badge></td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">School Sports House T-Shirt (Emerald)</td>
                                <td class="px-3 py-2.5 text-slate-500">Uniform</td>
                                <td class="px-3 py-2.5 font-bold text-amber-600">7 left</td>
                                <td class="px-3 py-2.5">₹ 350</td>
                                <td class="px-3 py-2.5 text-right"><x-badge variant="warning">Reorder Soon</x-badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Interactive Fee Collection Modal Demo -->
        <div
            x-show="feeModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs"
        >
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700" @click.outside="feeModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-base font-bold font-heading text-slate-900 dark:text-white">Collect Student Fee & Print Receipt</h3>
                    <button @click="feeModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 py-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Admission No / Student Name</label>
                        <input type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" value="ADM-2026-0042 - Aarav Santhosh (Class 8A)" readonly />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Term Installment</label>
                            <input type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" value="Term 3 (Jan 2027)" readonly />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Amount Due (₹)</label>
                            <input type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-bold text-emerald-600" value="₹ 10,000" readonly />
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Payment Mode</label>
                        <select class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                            <option>Cash Collection</option>
                            <option>UPI / QR Scan (GooglePay, PhonePe, Paytm)</option>
                            <option>Cheque / Demand Draft</option>
                            <option>Bank Direct NEFT / RTGS</option>
                        </select>
                    </div>

                    <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                        <p class="font-semibold">Next Receipt Number:</p>
                        <p class="font-mono text-sm font-bold mt-0.5">SCH/2026-27/REC-00143</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="feeModalOpen = false" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="feeModalOpen = false; window.showToast('Fee payment recorded & Receipt SCH/2026-27/REC-00143 generated!', 'success')" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                        Confirm & Print Receipt
                    </button>
                </div>
            </div>
        </div>

        <!-- Interactive Record Expense Modal Demo -->
        <div
            x-show="expenseModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs"
        >
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700" @click.outside="expenseModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-base font-bold font-heading text-slate-900 dark:text-white">Record Ledger Expense</h3>
                    <button @click="expenseModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 py-4 text-xs">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Expense Category</label>
                        <select class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                            <option>Maintenance & Repairs</option>
                            <option>Stationery & Paper Supplies</option>
                            <option>Electricity & Water Utilities</option>
                            <option>Laboratory Consumables</option>
                            <option>Sports & Cultural Events</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Vendor / Payee</label>
                            <input type="text" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" placeholder="e.g. Pudukkottai Paper Mart" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Amount (₹)</label>
                            <input type="number" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" placeholder="4500" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="expenseModalOpen = false" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-xs font-semibold cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="expenseModalOpen = false; window.showToast('Expense recorded successfully!', 'success')" class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                        Save Expense
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
