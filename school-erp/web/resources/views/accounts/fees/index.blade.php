<x-layouts.app>
    <x-slot:title>Fee Structures & Installment Schedules</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Fee Structures</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ newFeeModal: false }">
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
                    <span class="text-slate-800 dark:text-slate-200">Fee Structures</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Class-Wise Fee Schedule & Terms
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    3-Term installment framework (Term 1: June, Term 2: Oct, Term 3: Jan) with automatic student body allocation.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('accounts.fees.collect') }}"
                    class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-xs cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Cashier Collection Desk</span>
                </a>

                <button
                    type="button"
                    @click="newFeeModal = true"
                    class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-xs cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add Fee Component</span>
                </button>
            </div>
        </div>

        <!-- Class Selector Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-700">
            @foreach($classes as $c)
                <a
                    href="{{ route('accounts.fees.index', ['class_id' => $c->id]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $selectedClass && $selectedClass->id === $c->id ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}"
                >
                    {{ $c->name }}
                </a>
            @endforeach
        </div>

        <!-- Class Financial Summary Card -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Term 1 (June)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    ₹ {{ number_format($term1Total, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Admission & Tuition Fee</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Term 2 (October)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    ₹ {{ number_format($term2Total, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Mid-term Exam & Labs</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Term 3 (January)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    ₹ {{ number_format($term3Total, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Annual Session Fees</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Annual Tuition</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    ₹ {{ number_format($annualTotal, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Per enrolled student</p>
            </div>
        </div>

        <!-- Fee Heads Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">
                        Fee Components for {{ $selectedClass?->name ?? 'Selected Class' }}
                    </h2>
                    <p class="text-xs text-slate-500">Active breakdown of fee heads and due date schedules</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                    Academic Year {{ $currentYear?->name ?? '2026-2027' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Fee Head / Component</th>
                            <th class="px-4 py-3">Installment Term</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3 text-right">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($structures as $index => $fs)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-4 py-3.5 text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $fs->name }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $fs->term === 'Term 1' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300' : ($fs->term === 'Term 2' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                        {{ $fs->term }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300 font-mono">
                                    {{ $fs->due_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900 dark:text-white text-sm">
                                    ₹ {{ number_format($fs->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                    No fee structures configured for this class yet. Click "Add Fee Component" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($structures->count() > 0)
                        <tfoot class="bg-slate-50/80 dark:bg-slate-900/60 font-bold border-t border-slate-200 dark:border-slate-700 text-xs">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right uppercase text-slate-600 dark:text-slate-300">
                                    Annual Total Fee Payable:
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-base text-emerald-700 dark:text-emerald-400">
                                    ₹ {{ number_format($annualTotal, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Add Fee Component Modal -->
        <div
            x-show="newFeeModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newFeeModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Add Fee Component
                    </h3>
                    <button @click="newFeeModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounts.fees.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">
                    <input type="hidden" name="school_class_id" value="{{ $selectedClass?->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Class</label>
                        <input
                            type="text"
                            value="{{ $selectedClass?->name }}"
                            disabled
                            class="w-full text-xs rounded-xl bg-slate-100 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 p-2.5 text-slate-500 font-bold"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Fee Head Name</label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="e.g. Tuition Fee, Science Lab Fee, Term Exam"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Installment Term</label>
                            <select
                                name="term"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="Term 1">Term 1 (June)</option>
                                <option value="Term 2">Term 2 (October)</option>
                                <option value="Term 3">Term 3 (January)</option>
                                <option value="Annual">Annual One-Time</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Amount (₹)</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="amount"
                                required
                                placeholder="e.g. 15000"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Payment Due Date</label>
                        <input
                            type="date"
                            name="due_date"
                            required
                            value="{{ now()->addMonths(1)->toDateString() }}"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                        />
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newFeeModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs cursor-pointer"
                        >
                            Save & Allocate to Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
