<x-layouts.app>
    <x-slot:title>Unified Financial Transaction History</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Transactions History</x-slot:headerTitle>

    <div class="space-y-6">
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
                    <span class="text-slate-800 dark:text-slate-200">Transaction History</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Unified Transaction History & Audit Ledger
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Consolidated financial trail of Student Fees, Store POS sales, and Expense vouchers with full CSV export.
                </p>
            </div>

            <!-- CSV Export Button -->
            <a
                href="{{ route('accounts.reports.transactions.export', request()->all()) }}"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2 self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export Ledger (CSV)</span>
            </a>
        </div>

        <!-- Summary Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Inflows (Credits)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    ₹ {{ number_format($totalCredits, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Fees collection + School Store POS</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Outflows (Debits)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-rose-600 dark:text-rose-400">
                    ₹ {{ number_format($totalDebits, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Operational & capital vouchers</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Net Ledger Balance</span>
                <div class="mt-2 text-2xl font-bold font-heading {{ $netBalance >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600' }}">
                    ₹ {{ number_format($netBalance, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Operating cash surplus</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <form method="GET" action="{{ route('accounts.reports.transactions') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Transaction Stream</label>
                    <select
                        name="type"
                        class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                    >
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Streams</option>
                        <option value="fee" {{ $type === 'fee' ? 'selected' : '' }}>Fee Receipts</option>
                        <option value="store" {{ $type === 'store' ? 'selected' : '' }}>Store POS Sales</option>
                        <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Expense Vouchers</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">From Date</label>
                    <input
                        type="date"
                        name="from_date"
                        value="{{ $fromDate }}"
                        class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">To Date</label>
                    <input
                        type="date"
                        name="to_date"
                        value="{{ $toDate }}"
                        class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Search Keywords</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Bill No, Student, Vendor..."
                        class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                    />
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs cursor-pointer shadow-xs"
                    >
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions Ledger Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Reference / Bill</th>
                            <th class="px-4 py-3">Stream / Category</th>
                            <th class="px-4 py-3">Party / Beneficiary</th>
                            <th class="px-4 py-3">Payment Mode</th>
                            <th class="px-4 py-3 text-right">Credit (₹)</th>
                            <th class="px-4 py-3 text-right">Debit (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($transactions as $t)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $t['date'] }}
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $t['reference'] }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t['type'] === 'fee' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($t['type'] === 'store' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                        {{ $t['type_label'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $t['category'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $t['party'] }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-[10px] font-semibold">
                                        {{ $t['payment_method'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    @if($t['flow'] === 'credit')
                                        +₹ {{ number_format($t['amount'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                    @if($t['flow'] === 'debit')
                                        -₹ {{ number_format($t['amount'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                    No transactions found matching the specified filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
