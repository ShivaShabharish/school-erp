<x-layouts.app>
    <x-slot:title>Expense Voucher Register & Ledger</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Expense Ledger</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ newExpenseModal: false }">
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
                    <span class="text-slate-800 dark:text-slate-200">Expense Ledger</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Double-Entry Expense Voucher Register
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Campus maintenance, staff payroll, electricity, laboratory consumables, paper & printing vouchers.
                </p>
            </div>

            <button
                type="button"
                @click="newExpenseModal = true"
                class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2 self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Record Expense Voucher</span>
            </button>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Today's Disbursements</span>
                <div class="mt-2 text-2xl font-bold font-heading text-rose-600 dark:text-rose-400">
                    ₹ {{ number_format($todayTotal, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Cash & bank outflow today</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Month Total Expenditure</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    ₹ {{ number_format($monthTotal, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Across all budget categories</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Largest Cost Driver</span>
                <div class="mt-2 text-2xl font-bold font-heading text-amber-600 dark:text-amber-400">
                    Salaries & Maintenance
                </div>
                <p class="mt-1 text-xs text-slate-500">7th CPC payroll & physical upkeep</p>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto">
                <a
                    href="{{ route('accounts.expenses.index') }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ !$selectedCategory ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a
                        href="{{ route('accounts.expenses.index', ['category' => $cat]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $selectedCategory === $cat ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}"
                    >
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('accounts.expenses.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search vendor or bill no..."
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

        <!-- Expense Register Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Bill / Voucher No</th>
                            <th class="px-4 py-3">Expense Title & Vendor</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Payment Mode</th>
                            <th class="px-4 py-3 text-right">Disbursed (₹)</th>
                            <th class="px-4 py-3">Approved By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($expenses as $exp)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $exp->payment_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $exp->bill_no ?: 'EXP-' . $exp->id }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $exp->title }}</span>
                                    <span class="text-[10px] text-slate-400">Vendor: {{ $exp->vendor_name ?: 'Internal Voucher' }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        {{ $exp->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-[10px] font-semibold">
                                        {{ $exp->payment_method }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                    ₹ {{ number_format($exp->amount, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    {{ $exp->approver?->name ?? 'Accounts Officer' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                    No expense vouchers recorded for this period. Click "Record Expense Voucher" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>

        <!-- Add Expense Voucher Modal -->
        <div
            x-show="newExpenseModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newExpenseModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Record Expense Voucher
                    </h3>
                    <button @click="newExpenseModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounts.expenses.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Expense Title / Description *</label>
                        <input
                            type="text"
                            name="title"
                            required
                            placeholder="e.g. October 2026 TNEB Electricity Bill, Physics Lab Glassware"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Budget Category *</label>
                            <select
                                name="category"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Amount Disbursed (₹) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="1"
                                name="amount"
                                required
                                placeholder="e.g. 18500"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Vendor / Payee Name</label>
                            <input
                                type="text"
                                name="vendor_name"
                                placeholder="e.g. TNEB Pudukkottai, Apex Scientific"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bill / Invoice Reference</label>
                            <input
                                type="text"
                                name="bill_no"
                                placeholder="e.g. TNEB-OCT-2026-99"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Disbursement Date *</label>
                            <input
                                type="date"
                                name="payment_date"
                                required
                                value="{{ now()->toDateString() }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Payment Method *</label>
                            <select
                                name="payment_method"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="Bank Transfer">Bank Transfer (NEFT/RTGS)</option>
                                <option value="Cheque">Cheque</option>
                                <option value="UPI">UPI</option>
                                <option value="Cash">Cash Voucher</option>
                                <option value="Card">Corporate Card</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notes / Remarks</label>
                        <textarea
                            name="notes"
                            rows="2"
                            placeholder="Optional voucher notes..."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newExpenseModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs cursor-pointer"
                        >
                            Save Expense Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
