<x-layouts.app>
    <x-slot:title>System Security & Compliance Audit Trail</x-slot:title>
    <x-slot:headerTitle>Administration &bull; Audit Trail</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ payloadModal: false, activeLog: null, showPayload(log) { this.activeLog = log; this.payloadModal = true; } }">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Dashboard
                    </a>
                    <span>/</span>
                    <a href="{{ route('admin.settings.index') }}" class="hover:text-emerald-600">Settings</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Audit Trail</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Security & Compliance Audit Trail
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Immutable activity log capturing authentication, student admissions, fee collections, certificate issuances, and setting modifications.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.settings.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    &larr; Back to Settings
                </a>
            </div>
        </div>

        {{-- Filter Strip --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.settings.audit-logs') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Action Type</label>
                    <select name="action" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                        <option value="">All Actions</option>
                        @foreach($distinctActions as $act)
                            <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">User Account</label>
                    <select name="user_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.settings.audit-logs') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3 font-bold">Timestamp</th>
                            <th class="p-3 font-bold">User</th>
                            <th class="p-3 font-bold">Action</th>
                            <th class="p-3 font-bold">Target Entity</th>
                            <th class="p-3 font-bold">Client IP / Device</th>
                            <th class="p-3 font-bold text-right">Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition">
                                <td class="p-3 font-mono text-[11px] text-slate-500">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="p-3">
                                    @if($log->user)
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $log->user->name }}</div>
                                        <div class="text-[10px] text-slate-400 capitalize">{{ $log->user->role }}</div>
                                    @else
                                        <span class="text-slate-400 italic">System / Unauthenticated</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full font-mono font-bold text-[10px] bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-3 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                                    @if($log->model_type)
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-[10px] text-slate-500">
                                    <div>{{ $log->ip_address ?: '127.0.0.1' }}</div>
                                    <div class="truncate max-w-[150px] text-slate-400" title="{{ $log->user_agent }}">{{ $log->user_agent ?: 'Localhost' }}</div>
                                </td>
                                <td class="p-3 text-right">
                                    @if($log->old_values || $log->new_values)
                                        <button
                                            type="button"
                                            @click="showPayload({{ Js::from($log) }})"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 hover:bg-emerald-100 transition"
                                        >
                                            Inspect &rarr;
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[10px]">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    No audit log entries matching criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        {{-- JSON Payload Modal --}}
        <div x-show="payloadModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="payloadModal = false" class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-700 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Audit Event Details</h3>
                        <p class="text-xs text-slate-500" x-text="activeLog ? activeLog.action + ' • ' + activeLog.created_at : ''"></p>
                    </div>
                    <button @click="payloadModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-if="activeLog && activeLog.old_values">
                        <div>
                            <span class="text-xs font-bold text-rose-600 block mb-1">Previous State (Old Values):</span>
                            <pre class="bg-slate-50 dark:bg-slate-900 p-3 rounded-xl text-xs font-mono text-slate-700 dark:text-slate-300 overflow-x-auto border border-slate-200 dark:border-slate-700" x-text="JSON.stringify(activeLog.old_values, null, 2)"></pre>
                        </div>
                    </template>

                    <template x-if="activeLog && activeLog.new_values">
                        <div>
                            <span class="text-xs font-bold text-emerald-600 block mb-1">New State / Payload Attributes:</span>
                            <pre class="bg-slate-50 dark:bg-slate-900 p-3 rounded-xl text-xs font-mono text-slate-700 dark:text-slate-300 overflow-x-auto border border-slate-200 dark:border-slate-700" x-text="JSON.stringify(activeLog.new_values, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="payloadModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
