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
                today_fee_collection: {{ $metrics['today_fee_collection'] }},
                net_month_margin: {{ $metrics['net_month_margin'] }},
                total_alerts: {{ $alerts['total_alerts'] }}
            },
            searchQuery: '',
            filterStatus: 'all',
            lastUpdated: '{{ now()->format('h:i:s A') }}',
            isRefreshing: false,
            fetchLiveMetrics() {
                this.isRefreshing = true;
                fetch('{{ route('admin.metrics.live') }}')
                    .then(r => r.json())
                    .then(function(data) {
                        if (data.status === 'success') {
                            this.metrics = data.metrics;
                            this.lastUpdated = data.timestamp;
                        }
                    }.bind(this))
                    .catch(function(e) { console.error('Error fetching live metrics:', e); })
                    .finally(function() { this.isRefreshing = false; }.bind(this));
            }
        }"
        x-init="
            // Auto-refresh polling every 10 seconds per rule requirements
            setInterval(function() { fetchLiveMetrics(); }, 10000);
        "
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                        Admin Executive Command
                    </span>
                    <span class="text-xs text-slate-400">&bull; Session: {{ $currentYear?->name ?? '2026-2027' }}</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight mt-1">
                    Institution Operations Dashboard
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Test Subject Higher Secondary School &bull; Tamil Nadu State Board (Classes LKG to 12)
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-700/50 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Polling (10s) &bull; <span x-text="lastUpdated"></span>
                </span>

                <button
                    type="button"
                    @click="fetchLiveMetrics()"
                    class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold cursor-pointer shadow-xs"
                    title="Manual Refresh"
                >
                    <svg class="h-4 w-4" :class="{ 'animate-spin': isRefreshing }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- 5 Real-Time Stat Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Students Present -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md">
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
                <p class="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400" x-text="metrics.students_attendance_pct + '% school attendance'">
                    {{ $metrics['students_attendance_pct'] }}% school attendance
                </p>
            </div>

            <!-- 2. Staff on Duty -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Staff on Duty</span>
                    <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="metrics.staff_present + ' / {{ $metrics['total_staff'] }}'">
                    {{ $metrics['staff_present'] }} / {{ $metrics['total_staff'] }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Teaching & Administrative</p>
            </div>

            <!-- 3. Staff on Leave -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Staff on Leave</span>
                    <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 flex items-center justify-center">
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

            <!-- 4. Today's Fee Collection -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today's Fees</span>
                    <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-slate-900 dark:text-white" x-text="'₹ ' + Number(metrics.today_fee_collection).toLocaleString('en-IN')">
                    ₹ {{ number_format($metrics['today_fee_collection']) }}
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cash, UPI & Bank NEFT</p>
            </div>

            <!-- 5. Month Income vs Expense -->
            <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Month Net Margin</span>
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded">
                        Surplus
                    </span>
                </div>
                <div class="mt-3 text-2xl font-bold font-heading text-emerald-700 dark:text-emerald-400" x-text="'₹ ' + Number(metrics.net_month_margin).toLocaleString('en-IN')">
                    ₹ {{ number_format($metrics['net_month_margin']) }}
                </div>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 truncate">
                    Col: ₹{{ number_format($metrics['month_collection']) }} &bull; Exp: ₹{{ number_format($metrics['month_expenses']) }}
                </p>
            </div>
        </div>

        <!-- Middle Section: 7-Day Trend Chart + Alerts Widget -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 7-Day Attendance Trend Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div>
                        <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">7-Day Attendance Trend</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Turnout comparison across student body and faculty</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/40">
                            Avg: {{ $trendData['average_student_pct'] }}%
                        </span>
                    </div>
                </div>

                <div class="h-64 relative">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
                <script>
                    (function() {
                        function initAttendanceChart() {
                            var ctx = document.getElementById('attendanceTrendChart');
                            if (!ctx) return;
                            if (typeof Chart === 'undefined') {
                                setTimeout(initAttendanceChart, 100);
                                return;
                            }
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: @json($trendData['labels']),
                                    datasets: [
                                        {
                                            label: 'Students (%)',
                                            data: @json($trendData['students']),
                                            borderColor: '#047857',
                                            backgroundColor: 'rgba(4, 120, 87, 0.1)',
                                            tension: 0.35,
                                            fill: true,
                                            borderWidth: 2.5,
                                            pointRadius: 4,
                                            pointBackgroundColor: '#047857'
                                        },
                                        {
                                            label: 'Staff (%)',
                                            data: @json($trendData['staff']),
                                            borderColor: '#d97706',
                                            borderDash: [5, 5],
                                            tension: 0.25,
                                            fill: false,
                                            borderWidth: 2,
                                            pointRadius: 3,
                                            pointBackgroundColor: '#d97706'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { boxWidth: 12, font: { size: 11, family: 'Inter' } }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            min: 80,
                                            max: 100,
                                            ticks: {
                                                callback: function(v) { return v + '%'; }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initAttendanceChart);
                        } else {
                            initAttendanceChart();
                        }
                    })();
                </script>
            </div>

            <!-- Priority Operational Alerts Widget (Late staff, Fee Defaulters, Low Stock) -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">Operational Alerts</h2>
                            <span class="flex h-2 w-2 rounded-full bg-amber-500"></span>
                        </div>
                        <x-badge variant="warning" x-text="metrics.total_alerts + ' Active'">
                            {{ $alerts['total_alerts'] }} Active
                        </x-badge>
                    </div>

                    <div class="space-y-3">
                        <!-- Alert 1: Late Arriving Staff -->
                        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wide">Late Staff Arrivals</span>
                            </div>
                            <div class="space-y-1 mt-1.5">
                                @foreach($alerts['late_staff'] as $ls)
                                    <div class="flex items-center justify-between text-xs text-amber-950 dark:text-amber-100">
                                        <span class="font-semibold">{{ $ls['name'] }}</span>
                                        <span class="font-mono text-[11px] bg-amber-100/70 dark:bg-amber-900/50 px-1.5 py-0.5 rounded">{{ $ls['punch_time'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Alert 2: Fee Defaulters Past Grace Period -->
                        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-xs font-bold text-rose-900 dark:text-rose-200 uppercase tracking-wide">Fee Defaulters (Term 1)</span>
                            </div>
                            <div class="space-y-1 mt-1.5">
                                @foreach($alerts['fee_defaulters'] as $fd)
                                    <div class="flex items-center justify-between text-xs text-rose-950 dark:text-rose-100">
                                        <span>{{ $fd['name'] }}</span>
                                        <span class="font-bold text-rose-700 dark:text-rose-300">{{ $fd['overdue'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Alert 3: School Store Low Stock -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="h-4 w-4 text-slate-600 dark:text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Store Low-Stock</span>
                            </div>
                            <div class="space-y-1 mt-1.5">
                                @foreach($alerts['low_stock'] as $stk)
                                    <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300">
                                        <span class="truncate max-w-[160px]">{{ $stk['name'] }}</span>
                                        <span class="font-bold text-rose-600 dark:text-rose-400">{{ $stk['stock'] }} left</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <a
                        href="{{ route('accounts.dashboard') }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs cursor-pointer transition-colors"
                    >
                        <span>Open Accounts & Billing Area</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Class-wise Strength & Turnout Table (LKG to 12th Standard) with Search & Filter -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold font-heading text-slate-900 dark:text-white">Class-wise Strength & Today's Attendance</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Classes LKG to 12 across Tamil Nadu State Board sections</p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Search class or section..."
                            class="px-3 py-1.5 pl-8 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                        <svg class="h-3.5 w-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Turnout Filter -->
                    <select
                        x-model="filterStatus"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                    >
                        <option value="all">All Statuses</option>
                        <option value="Optimal">Optimal (&ge;95%)</option>
                        <option value="Normal">Normal (&ge;90%)</option>
                        <option value="Needs Attention">Needs Attention (&lt;90%)</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3.5">Class & Section</th>
                            <th class="px-5 py-3.5">Sanctioned Strength</th>
                            <th class="px-5 py-3.5 text-emerald-600 dark:text-emerald-400">Present Today</th>
                            <th class="px-5 py-3.5 text-rose-600 dark:text-rose-400">Absent Today</th>
                            <th class="px-5 py-3.5">Turnout %</th>
                            <th class="px-5 py-3.5 text-right">Turnout Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($classWiseData as $classGroup)
                            @foreach($classGroup['sections'] as $sec)
                                <tr
                                    x-show="
                                        (searchQuery === '' || '{{ strtolower($sec['full_name']) }}'.includes(searchQuery.toLowerCase())) &&
                                        (filterStatus === 'all' || '{{ $sec['status'] }}' === filterStatus)
                                    "
                                    class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors"
                                >
                                    <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $sec['full_name'] }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $sec['strength'] }} students
                                    </td>
                                    <td class="px-5 py-3 font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $sec['present'] }}
                                    </td>
                                    <td class="px-5 py-3 font-bold text-rose-600 dark:text-rose-400">
                                        {{ $sec['absent'] }}
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $sec['percentage'] >= 95 ? 'bg-emerald-600' : ($sec['percentage'] >= 90 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $sec['percentage'] }}%"></div>
                                            </div>
                                            <span>{{ $sec['percentage'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <x-badge variant="{{ $sec['percentage'] >= 95 ? 'success' : ($sec['percentage'] >= 90 ? 'warning' : 'danger') }}">
                                            {{ $sec['status'] }}
                                        </x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Summary Footer -->
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span>Showing 28 Active Class Sections (Classes LKG to 12)</span>
                <span>Institution Total: <strong class="text-slate-900 dark:text-white">{{ $metrics['total_students'] }} Students</strong></span>
            </div>
        </div>
    </div>
</x-layouts.app>
