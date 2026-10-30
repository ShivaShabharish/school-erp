<x-layouts.app>
    <x-slot:title>Student Concessions & Scholarships</x-slot:title>
    <x-slot:headerTitle>Accounts & Billing &bull; Concessions & Scholarships</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ newConcessionModal: false }">
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
                    <span class="text-slate-800 dark:text-slate-200">Fee Concessions</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Fee Concessions & Scholarship Grants
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Sibling discounts, merit scholarships, staff ward concessions, and RTE government scheme waivers.
                </p>
            </div>

            <button
                type="button"
                @click="newConcessionModal = true"
                class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-2 self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Grant New Concession</span>
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
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Beneficiaries</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    {{ $beneficiaryCount }} Students
                </div>
                <p class="mt-1 text-xs text-slate-500">Active concessions in {{ $currentYear?->name ?? '2026-27' }}</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Fixed Scholarship Grants</span>
                <div class="mt-2 text-2xl font-bold font-heading text-amber-600 dark:text-amber-400">
                    ₹ {{ number_format($totalWaiverAmount, 2) }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Total institutional aid disbursed</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Concession Policies</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    4 Active
                </div>
                <p class="mt-1 text-xs text-slate-500">Sibling (25%), Staff Ward (50%), Merit, RTE</p>
            </div>
        </div>

        <!-- Concessions Register Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">
                    Scholarship & Concession Beneficiary Roster
                </h2>
                <span class="text-xs text-slate-500 font-medium">
                    Showing {{ $concessions->firstItem() ?? 0 }}-{{ $concessions->lastItem() ?? 0 }} of {{ $concessions->total() }} records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Student Name</th>
                            <th class="px-4 py-3">Admission No</th>
                            <th class="px-4 py-3">Class & Sec</th>
                            <th class="px-4 py-3">Concession / Scheme</th>
                            <th class="px-4 py-3 text-right">Benefit Rate / Value</th>
                            <th class="px-4 py-3">Reason / Justification</th>
                            <th class="px-4 py-3">Approved By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($concessions as $index => $c)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 text-slate-400 font-mono">{{ $concessions->firstItem() + $index }}</td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $c->student?->full_name }}
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $c->student?->admission_no }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    Class {{ $c->student?->schoolClass?->name }} - {{ $c->student?->section?->name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                        {{ $c->title }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900 dark:text-white">
                                    @if($c->concession_type === 'percentage')
                                        {{ number_format($c->amount, 0) }}% Off Tuition
                                    @else
                                        ₹ {{ number_format($c->amount, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 max-w-xs truncate">
                                    {{ $c->reason ?? 'Institutional grant' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300 font-semibold">
                                    {{ $c->approver?->name ?? 'Admin Desk' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                    No concessions or scholarships registered yet. Click "Grant New Concession" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($concessions->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $concessions->links() }}
                </div>
            @endif
        </div>

        <!-- Grant Concession Modal -->
        <div
            x-show="newConcessionModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newConcessionModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Grant Student Concession / Scholarship
                    </h3>
                    <button @click="newConcessionModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounts.fees.concessions.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Student *</label>
                        <select
                            name="student_id"
                            required
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        >
                            <option value="">-- Choose Student Beneficiary --</option>
                            @foreach($classes as $cl)
                                <optgroup label="{{ $cl->name }}">
                                    @foreach($cl->students as $st)
                                        <option value="{{ $st->id }}">
                                            {{ $st->full_name }} ({{ $st->admission_no }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Concession Scheme / Category *</label>
                        <select
                            name="title"
                            required
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        >
                            <option value="Sibling Concession (25%)">Sibling Concession (25%)</option>
                            <option value="Staff Ward Discount (50%)">Staff Ward Discount (50%)</option>
                            <option value="Merit Scholarship">Merit Scholarship (Board Topper)</option>
                            <option value="RTE Govt Scheme Waiver">RTE Government Scheme (100%)</option>
                            <option value="Sports Excellence Scholarship">Sports Excellence Scholarship</option>
                            <option value="EWS Fee Assistance">EWS Fee Assistance</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Type *</label>
                            <select
                                name="concession_type"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Value (% or ₹) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="1"
                                name="amount"
                                required
                                placeholder="e.g. 25 or 5000"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reason / Justification</label>
                        <input
                            type="text"
                            name="reason"
                            placeholder="e.g. Second sibling enrolled in Class 6, Chairman approved"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newConcessionModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs cursor-pointer"
                        >
                            Apply Concession & Rebalance Dues
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
