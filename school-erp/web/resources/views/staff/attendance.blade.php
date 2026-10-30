<x-layouts.app>
    <x-slot:title>Faculty Biometric Attendance</x-slot:title>
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
                    <span class="text-slate-800 dark:text-slate-200">Daily Attendance</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Faculty Biometric Attendance Logs
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Real-time punch feed synchronized across gate turnstiles, staff room biometric scanners, and admin terminals.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.staff.biometric-simulator') }}"
                    class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-600/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.297M12 11v.01" />
                    </svg>
                    <span>Biometric Punch Simulator</span>
                </a>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Staff</span>
                <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1 block">
                    {{ $summary['total_staff'] }}
                </span>
                <span class="text-[10px] text-slate-500">Sanctioned Faculty</span>
            </div>

            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">On-Time (≤ 8:30 AM)</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $summary['present'] }}
                </span>
                <span class="text-[10px] text-emerald-700/80">Reporting Cutoff Met</span>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Late Arrivals</span>
                <span class="text-2xl font-extrabold font-heading text-amber-600 dark:text-amber-400 mt-1 block">
                    {{ $summary['late'] }}
                </span>
                <span class="text-[10px] text-amber-700/80">> 8:30 AM Flagged</span>
            </div>

            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400 block">On Approved Leave</span>
                <span class="text-2xl font-extrabold font-heading text-blue-600 dark:text-blue-400 mt-1 block">
                    {{ $summary['on_leave'] }}
                </span>
                <span class="text-[10px] text-blue-700/80">Authorized Absence</span>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs text-center col-span-2 md:col-span-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Attendance Rate</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $summary['turnout_pct'] }}%
                </span>
                <span class="text-[10px] text-slate-500">{{ $summary['total_punched'] }} Punched In</span>
            </div>
        </div>

        <!-- Filter & Attendance Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                    Attendance Log Roster: {{ \Carbon\Carbon::parse($date)->format('D, d F Y') }}
                </h3>

                <form method="GET" action="{{ route('admin.staff.attendance') }}" class="flex items-center gap-2">
                    <input
                        type="date"
                        name="date"
                        value="{{ $date }}"
                        class="px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                    <button
                        type="submit"
                        class="px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs transition-colors cursor-pointer"
                    >
                        Load Date
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Faculty / Staff Name</th>
                            <th class="px-5 py-3">Department</th>
                            <th class="px-5 py-3">In-Time</th>
                            <th class="px-5 py-3">Out-Time</th>
                            <th class="px-5 py-3">Terminal Device</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @foreach($staffMembers as $stf)
                            @php
                                $att = $stf->attendances->first();
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $stf->employee_code }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.staff.show', $stf) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400">
                                        {{ $stf->user?->name }}
                                    </a>
                                    <span class="block text-[10px] text-slate-400">{{ $stf->designation }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $stf->department }}
                                </td>
                                <td class="px-5 py-3.5 font-mono font-bold">
                                    {{ $att && $att->punch_in_time ? substr($att->punch_in_time, 0, 5) : '--:--' }}
                                </td>
                                <td class="px-5 py-3.5 font-mono">
                                    {{ $att && $att->punch_out_time ? substr($att->punch_out_time, 0, 5) : '--:--' }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-[11px] text-slate-500">
                                    {{ $att?->device_id ?: 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($att)
                                        @if($att->status === 'present')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                ON-TIME
                                            </span>
                                        @elseif($att->status === 'late')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                                LATE ARRIVAL
                                            </span>
                                        @elseif($att->status === 'leave' || $att->status === 'on_leave')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-300 dark:border-blue-800">
                                                ON LEAVE
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            ABSENT
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
