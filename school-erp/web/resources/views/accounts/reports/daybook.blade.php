<x-layouts.app>
    <x-slot:title>Daily Cash Day-Book Ledger</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Daily Cash Day-Book</x-slot:headerTitle>

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
                    <span class="text-slate-800 dark:text-slate-200">Day-Book</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Daily Cash Day-Book & Treasury Register
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Opening cash balance, daily counter receipts, vendor disbursements, and closing cash-in-hand tally.
                </p>
            </div>

            <!-- Date Selector Form -->
            <form method="GET" action="{{ route('accounts.reports.daybook') }}" class="flex items-center gap-2">
                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                />
                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs cursor-pointer"
                >
                    View Day-Book
                </button>
            </form>
        </div>

        <!-- Summary Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Cash Inflow</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    ₹ {{ number_format($daybook['total_inflow'], 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Collected today</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Cash Outflow</span>
                <div class="mt-2 text-2xl font-bold font-heading text-rose-600 dark:text-rose-400">
                    ₹ {{ number_format($daybook['total_outflow'], 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Disbursed today</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Net Day Balance</span>
                <div class="mt-2 text-2xl font-bold font-heading {{ $daybook['net_balance'] >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600' }}">
                    ₹ {{ number_format($daybook['net_balance'], 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Balance carried down</p>
            </div>
        </div>

        <!-- Day-Book Entry Chronology Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">
                        Day-Book Chronology for {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    </h2>
                    <p class="text-xs text-slate-500">All financial journal vouchers logged during official operational hours</p>
                </div>
                <button
                    type="button"
                    onclick="window.print()"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Print Day-Book</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">Reference / Bill</th>
                            <th class="px-4 py-3">Particulars / Description</th>
                            <th class="px-4 py-3">Party / Account</th>
                            <th class="px-4 py-3">Mode</th>
                            <th class="px-4 py-3 text-right">Inflow / Credit (₹)</th>
                            <th class="px-4 py-3 text-right">Outflow / Debit (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($daybook['entries'] as $entry)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono text-slate-500">{{ $entry['time'] }}</td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-800 dark:text-slate-200">{{ $entry['reference'] }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $entry['description'] }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $entry['category'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300">{{ $entry['party'] }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-[10px] font-semibold">{{ $entry['payment_method'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ $entry['type'] === 'inflow' ? '+₹ ' . number_format($entry['amount'], 2) : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                    {{ $entry['type'] === 'outflow' ? '-₹ ' . number_format($entry['amount'], 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                    No day-book transactions logged on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-900/60 font-bold border-t border-slate-200 dark:border-slate-700 text-xs">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-right uppercase text-slate-600 dark:text-slate-300">
                                Day Totals & Net Balance Carried Down:
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-sm text-emerald-700 dark:text-emerald-400">
                                +₹ {{ number_format($daybook['total_inflow'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-sm text-rose-700 dark:text-rose-400">
                                -₹ {{ number_format($daybook['total_outflow'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
