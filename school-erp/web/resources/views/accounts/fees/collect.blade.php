<x-layouts.app>
    <x-slot:title>Cashier Fee Collection Desk</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Fee Collection Desk</x-slot:headerTitle>

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
                    <a href="{{ route('accounts.fees.index') }}" class="hover:text-amber-600">Fee Master</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Cashier Counter</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Student Fee Collection & Receipt Counter
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Search student by Admission No or Name, inspect dues schedule, collect payment, and issue official DomPDF receipts.
                </p>
            </div>

            <!-- Student Search Form -->
            <form method="GET" action="{{ route('accounts.fees.collect') }}" class="flex items-center gap-2">
                <div class="relative w-72">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Admission No or Name..."
                        class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 py-2.5 pl-9 pr-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                    />
                    <svg class="h-4 w-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs cursor-pointer"
                >
                    Find Student
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @if(session('receipt_id'))
                    <a
                        href="{{ route('accounts.fees.receipt', session('receipt_id')) }}"
                        target="_blank"
                        class="px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download PDF Receipt</span>
                    </a>
                @endif
            </div>
        @endif

        @if($selectedStudent && $ledger)
            <!-- Student Financial Dossier & Payment Entry Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Student Info & Dues Schedule -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Student Header Card -->
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xl font-heading shadow-inner">
                                {{ substr($selectedStudent->first_name, 0, 1) }}{{ substr($selectedStudent->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-lg font-extrabold font-heading text-slate-900 dark:text-white">
                                    {{ $selectedStudent->full_name }}
                                </h2>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono font-bold text-amber-700 dark:text-amber-400">{{ $selectedStudent->admission_no }}</span>
                                    <span>&bull;</span>
                                    <span>Class {{ $selectedStudent->schoolClass?->name }} - Sec {{ $selectedStudent->section?->name }}</span>
                                    <span>&bull;</span>
                                    <span>Roll: {{ $selectedStudent->roll_number ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-[11px] font-bold text-slate-400 uppercase block">Outstanding Balance</span>
                            <span class="text-2xl font-black font-mono {{ $ledger['balance_due'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                ₹ {{ number_format($ledger['balance_due'], 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Dues Allocation Schedule Table -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <h3 class="font-bold font-heading text-sm text-slate-900 dark:text-white">
                                Fee Installments & Dues Schedule
                            </h3>
                            <span class="text-xs text-slate-500">
                                Total Paid: <strong class="text-emerald-600 font-mono">₹ {{ number_format($ledger['total_paid'], 2) }}</strong>
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="px-4 py-2.5">Fee Component</th>
                                        <th class="px-4 py-2.5">Term</th>
                                        <th class="px-4 py-2.5 text-right">Fee (₹)</th>
                                        <th class="px-4 py-2.5 text-right">Concession</th>
                                        <th class="px-4 py-2.5 text-right">Paid (₹)</th>
                                        <th class="px-4 py-2.5 text-right">Balance (₹)</th>
                                        <th class="px-4 py-2.5 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                                    @forelse($ledger['allocations'] as $alloc)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                            <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-100">
                                                {{ $alloc->feeStructure?->name ?? 'Tuition Fee' }}
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                {{ $alloc->feeStructure?->term ?? 'Term' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-slate-700 dark:text-slate-300">
                                                {{ number_format($alloc->amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-amber-600">
                                                {{ $alloc->concession_amount > 0 ? '-' . number_format($alloc->concession_amount, 2) : '0.00' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-emerald-600 font-bold">
                                                {{ number_format($alloc->paid_amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                                {{ number_format($alloc->balance_due, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $alloc->status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($alloc->status === 'partial' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                                    {{ $alloc->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                                No specific fee allocations found for this student.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment History (Past Receipts) -->
                    @if($ledger['receipts']->count() > 0)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
                            <h3 class="font-bold font-heading text-sm text-slate-900 dark:text-white mb-3">
                                Past Payment Receipts (This Academic Year)
                            </h3>
                            <div class="space-y-2">
                                @foreach($ledger['receipts'] as $pr)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 text-xs">
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono font-bold text-amber-700 dark:text-amber-400">{{ $pr->receipt_no }}</span>
                                            <span class="text-slate-400">&bull;</span>
                                            <span class="text-slate-600 dark:text-slate-300">{{ $pr->receipt_date->format('d M Y') }}</span>
                                            <span class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-[10px] font-semibold">{{ $pr->payment_method }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono font-bold text-emerald-600 text-sm">₹ {{ number_format($pr->amount_paid, 2) }}</span>
                                            <a
                                                href="{{ route('accounts.fees.receipt', $pr->id) }}"
                                                target="_blank"
                                                class="px-2.5 py-1 rounded bg-slate-200 hover:bg-slate-300 dark:bg-slate-600 dark:hover:bg-slate-500 text-slate-700 dark:text-slate-200 text-[11px] font-bold"
                                            >
                                                PDF
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Col: Cashier Collection Form -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs h-fit space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="font-extrabold font-heading text-base text-slate-900 dark:text-white">
                            Receive Fee Payment
                        </h3>
                        <p class="text-xs text-slate-500">Collects amount and generates sequential receipt</p>
                    </div>

                    <form method="POST" action="{{ route('accounts.fees.collect.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Amount to Collect (₹) *
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="1"
                                name="amount_paid"
                                required
                                value="{{ $ledger['balance_due'] > 0 ? $ledger['balance_due'] : '' }}"
                                placeholder="e.g. 15000"
                                class="w-full text-base font-bold font-mono rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"
                            />
                            @if($ledger['balance_due'] > 0)
                                <span class="text-[11px] text-amber-600 font-semibold mt-1 block">
                                    Current Dues: ₹ {{ number_format($ledger['balance_due'], 2) }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Payment Method *
                            </label>
                            <select
                                name="payment_method"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="Cash">Cash (Counter)</option>
                                <option value="UPI">UPI / Google Pay / PhonePe</option>
                                <option value="Bank Transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Demand Draft">Demand Draft (DD)</option>
                                <option value="Card">Debit / Credit Card</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Transaction Reference / Cheque No
                            </label>
                            <input
                                type="text"
                                name="transaction_reference"
                                placeholder="e.g. UPI Ref 3029482910 or Cheque #004521"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Receipt Date *
                            </label>
                            <input
                                type="date"
                                name="receipt_date"
                                required
                                value="{{ now()->toDateString() }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Notes / Remarks
                            </label>
                            <textarea
                                name="notes"
                                rows="2"
                                placeholder="Optional receipt remarks..."
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Collect Fee & Print Receipt</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Empty Search Prompt -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center">
                <div class="h-16 w-16 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 shadow-inner">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="font-heading font-extrabold text-base text-slate-800 dark:text-white">
                    Look Up Student Account
                </h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Enter the student's Admission Number (e.g. <code>SCH-2026-0001</code>) or full name above to load their dues dossier and record fee payment.
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
