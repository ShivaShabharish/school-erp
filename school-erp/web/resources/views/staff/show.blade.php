<x-layouts.app>
    <x-slot:title>Faculty Profile - {{ $staff->full_name }}</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.staff.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Staff Directory
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $staff->employee_code }}</span>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.staff.biometric-simulator') }}"
                    class="px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.297M12 11v.01" />
                    </svg>
                    <span>Simulate Bio Punch</span>
                </a>
            </div>
        </div>

        <!-- 360° Hero Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-900/10 via-transparent to-amber-900/10 dark:from-emerald-950/40 dark:to-slate-800">
                <div class="flex items-center gap-5">
                    <div class="h-20 w-20 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-extrabold font-heading text-2xl shadow-lg shadow-emerald-700/20 ring-4 ring-emerald-500/20">
                        {{ strtoupper(substr($staff->user?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white">
                                {{ $staff->full_name }}
                            </h1>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                {{ $staff->employee_code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-3">
                            <span><strong>Designation:</strong> {{ $staff->designation }}</span>
                            <span>•</span>
                            <span><strong>Department:</strong> {{ $staff->department }}</span>
                            <span>•</span>
                            <span><strong>Joined:</strong> {{ \Carbon\Carbon::parse($staff->joining_date)->format('d M Y') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Quick Stats Badges -->
                <div class="flex items-center gap-4">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 text-center min-w-[120px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Punctuality</span>
                        <span class="text-xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400">
                            {{ $punctualityRate }}%
                        </span>
                        <span class="text-[10px] text-slate-500 block">Biometric Score</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 text-center min-w-[120px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Net Salary</span>
                        <span class="text-xl font-extrabold font-heading font-mono text-slate-900 dark:text-white">
                            ₹{{ number_format($salaryBreakdown['net_salary'], 0) }}
                        </span>
                        <span class="text-[10px] text-slate-500 block">Monthly Payout</span>
                    </div>
                </div>
            </div>

            <!-- Tabbed Grid Panels -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 md:p-8">

                <!-- Left Column: Personal, Bank & Salary Breakdown -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Personal Information -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Personnel Information & Contact
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Official Email (Login)</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $staff->user?->email }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Contact Phone</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $staff->user?->phone }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Gender</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 capitalize">{{ $staff->gender }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Qualification</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $staff->qualification }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Date of Birth</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('d M Y') : 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Employment Status</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 capitalize">{{ $staff->status }}</span>
                            </div>
                            <div class="sm:col-span-3">
                                <span class="text-slate-400 block text-[11px]">Address</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->address ?: 'Pudukkottai, Tamil Nadu' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Structure Breakdown Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Standard Monthly Compensation Structure
                            </h3>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                7th Pay Commission Scale
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                            <!-- Earnings -->
                            <div class="space-y-2 bg-emerald-50/40 dark:bg-emerald-950/20 p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/40">
                                <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider block mb-2">Earnings Breakdown</span>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Basic Pay:</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['basic_salary'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Dearness Allowance (DA 20%):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['da_amount'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">House Rent Allowance (HRA 10%):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['hra_amount'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Conveyance / Academic Allowance:</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['special_allowance'], 2) }}</span>
                                </div>
                                <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800 flex justify-between font-bold text-emerald-700 dark:text-emerald-300">
                                    <span>Gross Salary:</span>
                                    <span class="font-mono">₹{{ number_format($salaryBreakdown['gross_salary'], 2) }}</span>
                                </div>
                            </div>

                            <!-- Deductions -->
                            <div class="space-y-2 bg-rose-50/40 dark:bg-rose-950/20 p-4 rounded-xl border border-rose-100 dark:border-rose-900/40">
                                <span class="text-[11px] font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider block mb-2">Deductions Breakdown</span>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Provident Fund (PF 12%):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['pf_deduction'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Professional Tax (PT):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($salaryBreakdown['tax_deduction'], 2) }}</span>
                                </div>
                                <div class="pt-8 border-t border-rose-200 dark:border-rose-800 flex justify-between font-bold text-rose-700 dark:text-rose-300">
                                    <span>Total Deductions:</span>
                                    <span class="font-mono">₹{{ number_format($salaryBreakdown['total_deductions'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Coordinates -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2">
                            <span>Disbursal Method: <strong>Direct Bank Transfer (NEFT)</strong></span>
                            <span>Bank: <strong>{{ $staff->bank_name ?: 'State Bank of India' }}</strong> (A/C: {{ $staff->bank_account_no ?: '38491028471' }} • IFSC: {{ $staff->bank_ifsc ?: 'SBIN0000898' }})</span>
                        </div>
                    </div>

                    <!-- Recent Payslips Issued -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Recent Monthly Payslips
                            </h3>
                            <a href="{{ route('admin.staff.payroll.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                View Full Payroll Sheet
                            </a>
                        </div>

                        @if($staff->payrolls->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                                        <tr>
                                            <th class="px-3 py-2">Payslip No</th>
                                            <th class="px-3 py-2">Month</th>
                                            <th class="px-3 py-2">Net Pay</th>
                                            <th class="px-3 py-2">Disbursed Date</th>
                                            <th class="px-3 py-2 text-right">PDF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($staff->payrolls as $pay)
                                            <tr>
                                                <td class="px-3 py-2.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                    {{ $pay->payslip_number }}
                                                </td>
                                                <td class="px-3 py-2.5 font-bold text-slate-800 dark:text-slate-200">
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $pay->month)->format('F Y') }}
                                                </td>
                                                <td class="px-3 py-2.5 font-mono font-bold text-slate-900 dark:text-white">
                                                    ₹{{ number_format($pay->net_salary, 2) }}
                                                </td>
                                                <td class="px-3 py-2.5 text-slate-500 text-[11px]">
                                                    {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : 'Pending' }}
                                                </td>
                                                <td class="px-3 py-2.5 text-right">
                                                    <a
                                                        href="{{ route('admin.staff.payroll.payslip', $pay) }}"
                                                        class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-[11px] transition-colors inline-flex items-center gap-1"
                                                    >
                                                        <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        <span>PDF</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No historical payslips generated on file for this employee.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Leave Quotas & Attendance Punches -->
                <div class="space-y-6">

                    <!-- Leave Quotas & Balances -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Leave Quotas (2026-2027)
                            </h3>
                            <a href="{{ route('admin.staff.leaves.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                Apply
                            </a>
                        </div>

                        <div class="space-y-3">
                            @foreach($staff->leaveQuotas as $q)
                                <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                                    <div class="flex items-center justify-between font-bold text-slate-900 dark:text-white">
                                        <span>{{ $q->leaveType->name }} ({{ $q->leaveType->code }})</span>
                                        <span class="text-emerald-600 dark:text-emerald-400 font-mono text-sm">
                                            {{ $q->remaining_days }} / {{ $q->allocated_days }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                                        @php
                                            $usedPct = $q->allocated_days > 0 ? min(100, ($q->used_days / $q->allocated_days) * 100) : 0;
                                        @endphp
                                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ 100 - $usedPct }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                                        <span>Used: {{ $q->used_days }} days</span>
                                        <span>Remaining: {{ $q->remaining_days }} days</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Biometric Punches Timeline -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Recent Biometric Punch Logs
                        </h3>

                        <div class="space-y-2.5 max-h-72 overflow-y-auto">
                            @forelse($staff->attendances as $att)
                                <div class="p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                            {{ \Carbon\Carbon::parse($att->date)->format('D, d M Y') }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            In: {{ $att->punch_in_time ? substr($att->punch_in_time, 0, 5) : '-' }} • Out: {{ $att->punch_out_time ? substr($att->punch_out_time, 0, 5) : '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        @if($att->status === 'present')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                ON TIME
                                            </span>
                                        @elseif($att->status === 'late')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                                LATE
                                            </span>
                                        @elseif($att->status === 'leave' || $att->status === 'on_leave')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                                LEAVE
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-slate-400 text-xs">
                                    No punch records found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
