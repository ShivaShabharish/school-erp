<x-layouts.app>
    <x-slot:title>Executive Admin Dashboard</x-slot:title>
    <x-slot:headerTitle>Executive Dashboard</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            metrics: {
                students_present: {{ $metrics['students_present'] }},
                students_attendance_pct: {{ $metrics['students_attendance_pct'] }},
                staff_present: {{ $metrics['staff_present'] }},
                staff_on_leave: {{ $metrics['staff_on_leave'] }},
                today_fee_collection: {{ $metrics['today_fee_collection'] }}
            },
            lastUpdated: '{{ now()->format('h:i:s A') }}',
            isRefreshing: false,
            fetchLiveMetrics() {
                this.isRefreshing = true;
                fetch('{{ route('admin.metrics.live') }}')
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.metrics = data.metrics;
                            this.lastUpdated = data.timestamp;
                        }
                    })
                    .catch(e => console.error('Error fetching live metrics:', e))
                    .finally(() => this.isRefreshing = false);
            }
        }"
        x-init="
            // Polling every 10 seconds per rule requirements
            setInterval(() => fetchLiveMetrics(), 10000);
        "
    >
        <!-- Top Status Bar & Refresh Indicator -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <h1 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">
                    School Operations Overview
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Academic Session: <strong class="text-slate-700 dark:text-slate-200">{{ $currentYear?->name ?? '2026-2027' }}</strong> &bull; LKG to 12th Standard ({{ $classesCount ?? 14 }} Classes)
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live polling (10s) &bull; Updated <span x-text="lastUpdated"></span>
                </span>

                <button
                    type="button"
                    @click="fetchLiveMetrics()"
                    class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold cursor-pointer"
                    title="Manual Refresh"
                >
                    <svg class="h-4 w-4" :class="{ 'animate-spin': isRefreshing }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- 5 Live Tiles Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Student Attendance Tile -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Students Present</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        LIVE
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <div class="text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="metrics.students_present + ' / {{ $metrics['total_students'] }}'">
                        {{ $metrics['students_present'] }} / {{ $metrics['total_students'] }}
                    </div>
                </div>
                <p class="mt-1 text-xs font-medium text-emerald-600 dark:text-emerald-400" x-text="metrics.students_attendance_pct + '% attendance rate'">
                    {{ $metrics['students_attendance_pct'] }}% attendance rate
                </p>
            </div>

            <!-- Staff Present Tile -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Staff On Duty</span>
                    <div class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="metrics.staff_present + ' / {{ $metrics['total_staff'] }}'">
                    {{ $metrics['staff_present'] }} / {{ $metrics['total_staff'] }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Teaching & Admin Staff</p>
            </div>

            <!-- Staff On Leave Tile -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Staff On Leave</span>
                    <div class="h-7 w-7 rounded-lg bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-amber-600 dark:text-amber-400" x-text="metrics.staff_on_leave">
                    {{ $metrics['staff_on_leave'] }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Approved leaves today</p>
            </div>

            <!-- Today's Collection Tile -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today's Fees</span>
                    <div class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="'₹ ' + Number(metrics.today_fee_collection).toLocaleString('en-IN')">
                    ₹ {{ number_format($metrics['today_fee_collection']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cash, UPI & Bank collections</p>
            </div>

            <!-- Monthly Income vs Expense Tile -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Net Month Margin</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded">
                        +45.1%
                    </span>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-emerald-700 dark:text-emerald-400">
                    ₹ {{ number_format($metrics['month_collection'] - $metrics['month_expenses']) }}
                </div>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 truncate">
                    Inc: ₹{{ number_format($metrics['month_collection']) }} &bull; Exp: ₹{{ number_format($metrics['month_expenses']) }}
                </p>
            </div>
        </div>

        <!-- Middle Section: Attendance Chart + Live Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 7-Day Attendance Trend Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold font-heading text-slate-900 dark:text-white">7-Day Attendance Trend</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Student & Staff Daily Turnout (%)</p>
                    </div>
                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-1 rounded-md">
                        Average: 95.8%
                    </span>
                </div>

                <div class="h-64 relative" x-data x-init="
                    const ctx = document.getElementById('attendanceTrendChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat (Half)', 'Today'],
                                datasets: [
                                    {
                                        label: 'Students (%)',
                                        data: [96.2, 95.8, 97.1, 94.9, 96.5, 93.8, 95.5],
                                        borderColor: '#047857',
                                        backgroundColor: 'rgba(4, 120, 87, 0.1)',
                                        tension: 0.3,
                                        fill: true
                                    },
                                    {
                                        label: 'Staff (%)',
                                        data: [98.0, 96.0, 100, 96.0, 98.0, 96.0, 96.0],
                                        borderColor: '#d97706',
                                        borderDash: [5, 5],
                                        tension: 0.3,
                                        fill: false
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: 'bottom' } },
                                scales: {
                                    y: { min: 85, max: 100 }
                                }
                            }
                        });
                    }
                ">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>

            <!-- Priority Alerts Widget -->
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h2 class="text-sm font-bold font-heading text-slate-900 dark:text-white">Operational Alerts</h2>
                        <x-badge variant="warning">3 Action Items</x-badge>
                    </div>

                    <div class="space-y-3">
                        <!-- Alert 1: Fee Defaulters -->
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40">
                            <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-amber-900 dark:text-amber-200">Term 1 Fee Defaulters</h4>
                                <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5">{{ $metrics['fee_defaulters_count'] }} students have overdue fees past the 15-day grace period.</p>
                            </div>
                        </div>

                        <!-- Alert 2: Low Stock Warning -->
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40">
                            <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-rose-900 dark:text-rose-200">School Store Inventory Alert</h4>
                                <p class="text-[11px] text-rose-800 dark:text-rose-300 mt-0.5">{{ $metrics['low_stock_items'] }} uniform sizes and notebook bundles reached threshold.</p>
                            </div>
                        </div>

                        <!-- Alert 3: Biometric Punch Info -->
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700">
                            <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Biometric API Sync</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Biometric machine terminal BIO-GATE-01 active.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <a
                        href="{{ route('accounts.dashboard') }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs cursor-pointer"
                    >
                        <span>Open Accounts & Billing Area</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Class-wise Strength & Turnout Table -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">Class-wise Strength & Today's Attendance</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Breakdown across Tamil Nadu State Board sections</p>
                </div>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Total Sanctioned: 312</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Class & Section</th>
                            <th class="px-5 py-3">Sanctioned Strength</th>
                            <th class="px-5 py-3 text-emerald-600">Present Today</th>
                            <th class="px-5 py-3 text-rose-600">Absent Today</th>
                            <th class="px-5 py-3">Attendance %</th>
                            <th class="px-5 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @php
                        $sampleClasses = [
                            ['name' => 'LKG - Section A', 'strength' => 22, 'present' => 21, 'absent' => 1],
                            ['name' => 'UKG - Section A', 'strength' => 24, 'present' => 24, 'absent' => 0],
                            ['name' => 'Class 1 - Section A', 'strength' => 26, 'present' => 25, 'absent' => 1],
                            ['name' => 'Class 5 - Section A', 'strength' => 28, 'present' => 27, 'absent' => 1],
                            ['name' => 'Class 8 - Section A', 'strength' => 30, 'present' => 29, 'absent' => 1],
                            ['name' => 'Class 10 - Section A', 'strength' => 32, 'present' => 31, 'absent' => 1],
                            ['name' => 'Class 12 - Section A (Science)', 'strength' => 30, 'present' => 29, 'absent' => 1],
                        ];
                        @endphp
                        @foreach($sampleClasses as $cls)
                            @php $pct = round(($cls['present'] / $cls['strength']) * 100, 1); @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">{{ $cls['name'] }}</td>
                                <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $cls['strength'] }}</td>
                                <td class="px-5 py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ $cls['present'] }}</td>
                                <td class="px-5 py-3 font-semibold text-rose-600 dark:text-rose-400">{{ $cls['absent'] }}</td>
                                <td class="px-5 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $pct }}%</td>
                                <td class="px-5 py-3 text-right">
                                    <x-badge variant="{{ $pct >= 95 ? 'success' : 'warning' }}">
                                        {{ $pct >= 95 ? 'Optimal' : 'Needs Review' }}
                                    </x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
