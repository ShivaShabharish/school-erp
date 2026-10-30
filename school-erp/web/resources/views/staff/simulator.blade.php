<x-layouts.app>
    <x-slot:title>Biometric Punch Simulator</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div
        class="max-w-4xl mx-auto space-y-6"
        x-data="{
            selectedStaff: '{{ $staffMembers->first()->id ?? '' }}',
            selectedDevice: 'BIO-GATE-01',
            customTime: '',
            isSubmitting: false,
            result: null,
            error: null,
            async triggerPunch() {
                this.isSubmitting = true;
                this.result = null;
                this.error = null;

                try {
                    const response = await fetch('{{ route('admin.staff.simulate-punch') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            staff_id: this.selectedStaff,
                            device_id: this.selectedDevice,
                            timestamp: this.customTime || null
                        })
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        this.error = data.message || 'Error executing biometric punch.';
                    } else {
                        this.result = data;
                    }
                } catch (err) {
                    this.error = 'Network error contacting simulator.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }"
    >
        <!-- Breadcrumb & Back -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.staff.attendance') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Attendance Log
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Biometric Simulator</span>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                Admin Diagnostic Tool
            </span>
        </div>

        <!-- Simulator Machine Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">
                        Biometric Machine Punch Simulator
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Test and demonstrate optical fingerprint machine punches against the attendance engine.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600 text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>API Endpoint: <code>POST /api/v1/biometric/punch</code></span>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Terminal Scanner Device *
                        </label>
                        <select
                            x-model="selectedDevice"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500"
                        >
                            @foreach($devices as $dev)
                                <option value="{{ $dev['id'] }}">{{ $dev['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Select Faculty / Staff Member *
                        </label>
                        <select
                            x-model="selectedStaff"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500"
                        >
                            @foreach($staffMembers as $st)
                                <option value="{{ $st->id }}">{{ $st->employee_code }} — {{ $st->user?->name }} ({{ $st->designation }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Simulated Punch Timestamp (Leave blank for current wall clock)
                        </label>
                        <input
                            type="datetime-local"
                            x-model="customTime"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500"
                        />
                        <p class="text-[10px] text-slate-400 mt-1">
                            Note: Attendance marks <strong>ON-TIME</strong> if punch time &le; 08:30 AM; punches after 08:30 AM are flagged as <strong>LATE ARRIVAL</strong>.
                        </p>
                    </div>
                </div>

                <!-- Simulate Button -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        First punch records <strong>Punch In</strong>; subsequent punches register <strong>Punch Out</strong>.
                    </span>
                    <button
                        type="button"
                        @click="triggerPunch()"
                        :disabled="isSubmitting"
                        class="px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-lg shadow-amber-600/25 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.297M12 11v.01" />
                        </svg>
                        <span x-text="isSubmitting ? 'Scanning Biometric...' : 'Simulate Fingerprint Scan'"></span>
                    </button>
                </div>

                <!-- Live Results Banner -->
                <template x-if="result">
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-xs space-y-2">
                        <div class="flex items-center justify-between font-bold text-emerald-800 dark:text-emerald-300">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="result.message"></span>
                            </span>
                            <span class="px-2 py-0.5 rounded uppercase font-extrabold text-[10px]" :class="result.status === 'present' ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900'" x-text="result.status"></span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-[11px] text-slate-700 dark:text-slate-300 pt-2 border-t border-emerald-200 dark:border-emerald-800">
                            <div><strong>Action:</strong> <span x-text="result.action"></span></div>
                            <div><strong>Punch In:</strong> <span x-text="result.punch_in_time"></span></div>
                            <div><strong>Punch Out:</strong> <span x-text="result.punch_out_time || 'None'"></span></div>
                            <div><strong>Device:</strong> <span x-text="result.device_id"></span></div>
                        </div>
                    </div>
                </template>

                <template x-if="error">
                    <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-300 font-bold flex items-center gap-2">
                        <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="error"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Recent Simulated / Scanned Punches Feed -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                    Recent Real-Time Biometric Punch Feed
                </h3>
                <span class="text-xs text-slate-400">Live Device Logs</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Faculty Name</th>
                            <th class="px-5 py-3">In-Time</th>
                            <th class="px-5 py-3">Out-Time</th>
                            <th class="px-5 py-3">Terminal Device</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @foreach($todayPunches as $p)
                            <tr>
                                <td class="px-5 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $p->staff?->employee_code }}
                                </td>
                                <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                    {{ $p->staff?->user?->name }}
                                </td>
                                <td class="px-5 py-3 font-mono font-bold">
                                    {{ $p->punch_in_time ? substr($p->punch_in_time, 0, 5) : '-' }}
                                </td>
                                <td class="px-5 py-3 font-mono">
                                    {{ $p->punch_out_time ? substr($p->punch_out_time, 0, 5) : '-' }}
                                </td>
                                <td class="px-5 py-3 font-mono text-[11px] text-slate-400">
                                    {{ $p->device_id ?: 'BIO-GATE-01' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $p->status === 'present' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
