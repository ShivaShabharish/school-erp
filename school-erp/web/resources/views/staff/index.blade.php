<x-layouts.app>
    <x-slot:title>Staff & HR Directory</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Faculty & Staff Directory
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Manage institutional personnel, designations, biometric attendance logs, leave quotas, and monthly payrolls.
                </p>
            </div>

            <!-- Operations Navigation Actions -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.staff.attendance') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Live Attendance</span>
                </a>

                <a
                    href="{{ route('admin.staff.biometric-simulator') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.297M12 11v.01" />
                    </svg>
                    <span>Bio-Simulator</span>
                </a>

                <a
                    href="{{ route('admin.staff.leaves.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Leave Quotas</span>
                </a>

                <a
                    href="{{ route('admin.staff.payroll.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                    </svg>
                    <span>Salary & Payslips</span>
                </a>

                <a
                    href="{{ route('admin.staff.create') }}"
                    class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Onboard Staff</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card
                title="Total Active Personnel"
                :value="$totalActive"
                subtitle="Permanent & Contractual Faculty"
            />
            <x-stat-card
                title="Teaching Faculty"
                :value="$totalTeaching"
                subtitle="PGT, TGT & Primary Teachers"
            />
            <x-stat-card
                title="Administrative & Support"
                :value="$totalNonTeaching"
                subtitle="Office, Accounts, Lab & Transport"
                accent="amber"
            />
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name, employee code, phone..."
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                </div>

                <div>
                    <select
                        name="department"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Status: Active Only</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Status: All Records</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold transition-all text-center cursor-pointer shadow-xs"
                    >
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'department', 'status']))
                        <a
                            href="{{ route('admin.staff.index') }}"
                            class="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Staff Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Faculty / Staff Name</th>
                            <th class="px-5 py-3">Designation & Dept</th>
                            <th class="px-5 py-3">Contact</th>
                            <th class="px-5 py-3">Basic Salary</th>
                            <th class="px-5 py-3">Today's Punch</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @forelse($staffMembers as $stf)
                            @php
                                $todayAtt = $stf->attendances->first();
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $stf->employee_code }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $stf->user?->name }}</div>
                                    <span class="text-[11px] text-slate-400">{{ $stf->qualification }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $stf->designation }}</div>
                                    <span class="inline-block px-2 py-0.5 mt-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        {{ $stf->department }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600 dark:text-slate-400">
                                    <div>{{ $stf->user?->phone }}</div>
                                    <div class="text-[10px] text-slate-400 font-sans">{{ $stf->user?->email }}</div>
                                </td>
                                <td class="px-5 py-3.5 font-bold font-mono text-slate-900 dark:text-white">
                                    ₹{{ number_format($stf->basic_salary, 2) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($todayAtt)
                                        @if($todayAtt->status === 'present')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                ON-TIME ({{ substr($todayAtt->punch_in_time, 0, 5) }})
                                            </span>
                                        @elseif($todayAtt->status === 'late')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                                LATE ({{ substr($todayAtt->punch_in_time, 0, 5) }})
                                            </span>
                                        @elseif($todayAtt->status === 'leave' || $todayAtt->status === 'on_leave')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                                ON LEAVE
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                            NOT PUNCHED
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a
                                        href="{{ route('admin.staff.show', $stf) }}"
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-[11px] font-bold transition-all border border-slate-300 dark:border-slate-600 inline-flex items-center gap-1"
                                    >
                                        <span>360° Profile</span>
                                        <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    No staff records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffMembers->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
