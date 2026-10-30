<x-layouts.app>
    <x-slot:title>Monthly Financial Statement</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Monthly Statement</x-slot:headerTitle>

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
                    <span class="text-slate-800 dark:text-slate-200">Monthly Statement</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Monthly Income & Expenditure Statement
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Operating surplus/deficit audit, fee revenue streams, store profit contribution, and expense head shares.
                </p>
            </div>

            <!-- Month/Year Filter Form -->
            <form method="GET" action="{{ route('accounts.reports.monthly') }}" class="flex items-center gap-2">
                <select
                    name="month"
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                >
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>

                <select
                    name="year"
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                >
                    <option value="2026" {{ $year === 2026 ? 'selected' : '' }}>2026</option>
                    <option value="2027" {{ $year === 2027 ? 'selected' : '' }}>2027</option>
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs cursor-pointer"
                >
                    Generate Report
                </button>
            </form>
        </div>

        <!-- Statement Month Badge Banner -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold block mb-1">Financial Statement Period</span>
                <h2 class="text-2xl font-extrabold font-heading">
                    {{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }}
                </h2>
                <p class="text-xs text-slate-300 mt-1">
                    Operating Margin: <span class="font-bold text-emerald-400">{{ $operatingMargin }}%</span> &bull; Status: Audit Balanced
                </p>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-400 uppercase font-semibold block">Net Operating Surplus</span>
                <span class="text-3xl font-black font-mono {{ $netSurplus >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    ₹ {{ number_format($netSurplus, 2) }}
                </span>
            </div>
        </div>

        <!-- 2 Columns: Income vs Expenditure Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Col: Revenue & Inflows -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        <h3 class="font-extrabold font-heading text-base text-slate-900 dark:text-white">
                            Revenue Streams (Income)
                        </h3>
                    </div>
                    <span class="font-mono font-bold text-emerald-600 text-base">
                        ₹ {{ number_format($totalIncome, 2) }}
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Tuition & Academic Term Fees</span>
                            <span class="text-[10px] text-slate-400">Student fee collection counter</span>
                        </div>
                        <span class="font-mono font-bold text-sm text-slate-800 dark:text-slate-200">
                            ₹ {{ number_format($feeIncome, 2) }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">School Store & Uniforms POS</span>
                            <span class="text-[10px] text-slate-400">Gross retail receipts</span>
                        </div>
                        <span class="font-mono font-bold text-sm text-slate-800 dark:text-slate-200">
                            ₹ {{ number_format($storeIncome, 2) }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-emerald-800 dark:text-emerald-300 block">School Store Net Profit Contribution</span>
                            <span class="text-[10px] text-emerald-600">Retail sales less inventory cost</span>
                        </div>
                        <span class="font-mono font-bold text-sm text-emerald-700 dark:text-emerald-300">
                            +₹ {{ number_format($storeProfit, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Col: Expenditures by Category -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                        <h3 class="font-extrabold font-heading text-base text-slate-900 dark:text-white">
                            Operating Outflows (Expenses)
                        </h3>
                    </div>
                    <span class="font-mono font-bold text-rose-600 text-base">
                        ₹ {{ number_format($totalExpense, 2) }}
                    </span>
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto">
                    @forelse($expenses as $catExp)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $catExp->category }}</span>
                            <span class="font-mono font-bold text-rose-600 dark:text-rose-400">
                                ₹ {{ number_format($catExp->total, 2) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            No expense entries recorded for this calendar month.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
