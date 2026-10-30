<x-layouts.app>
    <x-slot:title>Parent Portal</x-slot:title>
    <x-slot:headerTitle>Parent & Ward Portal</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Ward Overview Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-heading font-extrabold text-2xl flex items-center justify-center shadow-xs border border-emerald-200 dark:border-emerald-800/40">
                    {{ substr($ward['name'], 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">{{ $ward['name'] }}</h1>
                        <x-badge variant="success">Active Student</x-badge>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $ward['class_section'] }} &bull; Roll No: {{ $ward['roll_no'] }} &bull; Admission No: <strong class="font-mono text-slate-700 dark:text-slate-200">{{ $ward['admission_no'] }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-left border border-slate-200 dark:border-slate-600">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Attendance</span>
                    <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">{{ $ward['attendance_pct'] }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-left border border-slate-200 dark:border-slate-600">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Latest Term Grade</span>
                    <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ $ward['recent_exam_grade'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-left border border-slate-200 dark:border-slate-600">
                    <span class="block text-[10px] font-bold uppercase text-slate-400">Fee Status</span>
                    <span class="text-base font-extrabold text-emerald-600">All Cleared</span>
                </div>
            </div>
        </div>

        <!-- Academic & Fee Quick Access -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Announcements -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">School Circulars & Announcements</h3>
                    <x-badge variant="neutral">Recent</x-badge>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] text-emerald-600 font-bold uppercase">Academic</span>
                        <h4 class="font-bold text-slate-900 dark:text-white mt-0.5">Term 2 Examination Schedule Released</h4>
                        <p class="text-slate-500 mt-1">Exams commence from November 18, 2026. Detailed timetable is available on the portal.</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] text-amber-600 font-bold uppercase">Event</span>
                        <h4 class="font-bold text-slate-900 dark:text-white mt-0.5">Annual Sports Day 2026</h4>
                        <p class="text-slate-500 mt-1">Scheduled for December 12th. Parents are cordially invited.</p>
                    </div>
                </div>
            </div>

            <!-- Fee Receipts History -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">Fee Receipts & Invoices</h3>
                    <span class="text-xs text-emerald-600 font-bold">No Dues Outstanding</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">SCH/2026-27/REC-00042</span>
                            <p class="text-slate-500 text-[11px]">Term 2 Installment &bull; Paid via UPI</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 dark:text-white">₹ 10,000</span>
                            <span class="block text-[10px] text-emerald-600 font-semibold">Verified Receipt</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">SCH/2026-27/REC-00018</span>
                            <p class="text-slate-500 text-[11px]">Term 1 Installment &bull; Paid via NetBanking</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 dark:text-white">₹ 12,000</span>
                            <span class="block text-[10px] text-emerald-600 font-semibold">Verified Receipt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
