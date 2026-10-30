<x-layouts.app>
    <x-slot:title>Faculty Monthly Payroll Sheet</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.staff.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Staff Directory
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Payroll Sheet</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Monthly Salary Sheet & Payslips
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    7th Pay Scale compliance with Dearness Allowance (DA 20%), HRA (10%), PF deductions (12%) and PDF payslip generation.
                </p>
            </div>

            <!-- Month Selector Form -->
            <form method="GET" action="{{ route('admin.staff.payroll.index') }}" class="flex items-center gap-2 self-start md:self-auto">
                <input
                    type="month"
                    name="month"
                    value="{{ $month }}"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                />
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs transition-colors cursor-pointer shadow-xs"
                >
                    Load Month
                </button>
            </form>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Payroll Cohort</span>
                <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1 block">
                    {{ $staffCount }} Faculty
                </span>
                <span class="text-[10px] text-slate-500">Active salaried personnel</span>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Gross Compensation</span>
                <span class="text-2xl font-extrabold font-heading font-mono text-slate-900 dark:text-white mt-1 block">
                    ₹{{ number_format($totalGross, 2) }}
                </span>
                <span class="text-[10px] text-slate-500">Basic + DA + HRA + Special</span>
            </div>

            <div class="p-5 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900 shadow-xs">
                <span class="text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider block">Total Deductions</span>
                <span class="text-2xl font-extrabold font-heading font-mono text-rose-600 dark:text-rose-400 mt-1 block">
                    ₹{{ number_format($totalDeductions, 2) }}
                </span>
                <span class="text-[10px] text-rose-700/80">PF (12%) & Professional Tax</span>
            </div>

            <div class="p-5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 shadow-xs">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Total Net Disbursed</span>
                <span class="text-2xl font-extrabold font-heading font-mono text-emerald-600 dark:text-emerald-400 mt-1 block">
                    ₹{{ number_format($totalPayout, 2) }}
                </span>
                <span class="text-[10px] text-emerald-700/80">Transferred via Bank NEFT</span>
            </div>
        </div>

        <!-- Payroll Sheet Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                        Salary Disbursal Register: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                    </h3>
                    <p class="text-[11px] text-slate-400">All amounts in Indian Rupees (₹)</p>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-300 dark:border-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>Disbursal Confirmed</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Payslip No</th>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Attendance</th>
                            <th class="px-4 py-3">Basic Pay</th>
                            <th class="px-4 py-3">DA (20%)</th>
                            <th class="px-4 py-3">HRA (10%)</th>
                            <th class="px-4 py-3">Gross</th>
                            <th class="px-4 py-3">PF Deduct</th>
                            <th class="px-4 py-3">Net Salary</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @foreach($payrolls as $p)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400 text-[11px]">
                                    {{ $p->payslip_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $p->staff?->user?->name }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $p->staff?->employee_code }} • {{ $p->staff?->designation }}</span>
                                </td>
                                <td class="px-4 py-3 text-[11px]">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $p->present_days }}</span> / {{ $p->working_days }} Days
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    ₹{{ number_format($p->basic_salary, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    ₹{{ number_format($p->da_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    ₹{{ number_format($p->hra_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                    ₹{{ number_format($p->gross_salary, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono text-rose-600 dark:text-rose-400">
                                    ₹{{ number_format($p->pf_deduction, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono font-extrabold text-emerald-700 dark:text-emerald-400">
                                    ₹{{ number_format($p->net_salary, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a
                                        href="{{ route('admin.staff.payroll.payslip', $p) }}"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] transition-all shadow-xs inline-flex items-center gap-1 cursor-pointer"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Payslip PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
