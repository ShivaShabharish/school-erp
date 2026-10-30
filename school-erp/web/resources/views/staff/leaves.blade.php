<x-layouts.app>
    <x-slot:title>Faculty Leave Management</x-slot:title>
    <x-slot:headerTitle>Staff & HR</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            applyModal: false,
            rejectModal: false,
            selectedAppId: null,
            openReject(id) {
                this.selectedAppId = id;
                this.rejectModal = true;
            }
        }"
    >
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
                    <span class="text-slate-800 dark:text-slate-200">Leave Management</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Faculty Leave Quotas & Approvals
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Track Casual Leave (CL), Medical Leave (ML) and Earned Leave (EL) balances and authorize faculty absence requests.
                </p>
            </div>

            <!-- Action Button -->
            <button
                type="button"
                @click="applyModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Submit Leave Request</span>
            </button>
        </div>

        <!-- Filter Tabs & Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a
                href="{{ route('admin.staff.leaves.index', ['status' => 'all']) }}"
                class="p-4 rounded-xl border {{ $status === 'all' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Requests</span>
                <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1 block">
                    {{ $counts['total'] }}
                </span>
                <span class="text-[10px] text-slate-500">All applications on file</span>
            </a>

            <a
                href="{{ route('admin.staff.leaves.index', ['status' => 'pending']) }}"
                class="p-4 rounded-xl border {{ $status === 'pending' ? 'border-amber-600 bg-amber-50/50 dark:bg-amber-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-500 block">Pending Review</span>
                <span class="text-2xl font-extrabold font-heading text-amber-600 dark:text-amber-400 mt-1 block">
                    {{ $counts['pending'] }}
                </span>
                <span class="text-[10px] text-slate-500">Awaiting Principal Action</span>
            </a>

            <a
                href="{{ route('admin.staff.leaves.index', ['status' => 'approved']) }}"
                class="p-4 rounded-xl border {{ $status === 'approved' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-500 block">Approved</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $counts['approved'] }}
                </span>
                <span class="text-[10px] text-slate-500">Quota Deducted & Logged</span>
            </a>

            <a
                href="{{ route('admin.staff.leaves.index', ['status' => 'rejected']) }}"
                class="p-4 rounded-xl border {{ $status === 'rejected' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500 block">Rejected</span>
                <span class="text-2xl font-extrabold font-heading text-rose-600 dark:text-rose-400 mt-1 block">
                    {{ $counts['rejected'] }}
                </span>
                <span class="text-[10px] text-slate-500">Declined Absence</span>
            </a>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Faculty / Staff</th>
                            <th class="px-5 py-3">Leave Category</th>
                            <th class="px-5 py-3">Duration & Dates</th>
                            <th class="px-5 py-3">Days</th>
                            <th class="px-5 py-3">Reason</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $app->staff?->user?->name }}</div>
                                    <span class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400">{{ $app->staff?->employee_code }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $app->leaveType?->name }}</span>
                                    <span class="block text-[10px] font-mono text-slate-400">{{ $app->leaveType?->code }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($app->start_date)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($app->end_date)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 font-bold font-mono text-slate-900 dark:text-white">
                                    {{ $app->days_count }} Day(s)
                                </td>
                                <td class="px-5 py-3.5 max-w-xs truncate" title="{{ $app->reason }}">
                                    {{ $app->reason }}
                                    @if($app->remarks)
                                        <div class="text-[10px] text-slate-400 italic">Remark: {{ $app->remarks }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($app->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                            APPROVED
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            REJECTED
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                            PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if($app->status === 'pending')
                                        <div class="inline-flex items-center gap-1.5">
                                            <form action="{{ route('admin.staff.leaves.approve', $app) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] shadow-xs cursor-pointer"
                                                >
                                                    Approve
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                @click="openReject({{ $app->id }})"
                                                class="px-2.5 py-1 rounded border border-rose-300 dark:border-rose-700 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-bold text-[11px] cursor-pointer"
                                            >
                                                Reject
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Decided</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    No leave applications on record under this status filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

        <!-- Modal: Apply Leave Form -->
        <div
            x-show="applyModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="applyModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Submit Leave Application
                    </h3>
                    <button type="button" @click="applyModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.staff.leaves.apply') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Staff Member *</label>
                        <select name="staff_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($staffMembers as $stf)
                                <option value="{{ $stf->id }}">{{ $stf->employee_code }} — {{ $stf->user?->name }} ({{ $stf->designation }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Leave Category *</label>
                        <select name="leave_type_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->code }} — {{ $lt->days_per_year }} days/yr)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Start Date *</label>
                            <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">End Date *</label>
                            <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reason for Absence *</label>
                        <textarea name="reason" rows="2" required placeholder="Specify medical, personal or academic reason..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="applyModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Reject Remarks Form -->
        <div
            x-show="rejectModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="rejectModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-rose-600">
                        Decline Leave Request
                    </h3>
                    <button type="button" @click="rejectModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAppId">
                    <form :action="`/admin/staff/leaves/${selectedAppId}/reject`" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Reason for Rejection / Administrative Remarks
                            </label>
                            <textarea
                                name="remarks"
                                rows="2"
                                placeholder="e.g. Critical examination duties scheduled on requested dates..."
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-layouts.app>
