<x-layouts.app>
    <x-slot:title>Teacher Portal</x-slot:title>
    <x-slot:headerTitle>Teacher Portal</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-emerald-700 text-white font-heading font-extrabold text-xl flex items-center justify-center shadow-md">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div>
                    <h1 class="text-xl font-extrabold font-heading text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Faculty & Class Teacher &bull; Department of Mathematics</p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-semibold border border-emerald-200 dark:border-emerald-800/40">
                    Monthly Attendance: {{ $metrics['monthly_attendance_pct'] }}%
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold">
                    Leave Balance: {{ $metrics['leave_balance'] }} Days
                </span>
            </div>
        </div>

        <!-- Today's Teaching Schedule -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-white">Today's Class Schedule</h3>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">5 Periods Scheduled</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
                <div class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-950/20">
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Period 1 (09:00 - 09:45)</span>
                    <h4 class="font-bold text-slate-900 dark:text-white mt-1">Class 8 - Section A</h4>
                    <p class="text-slate-500 mt-0.5">Algebra & Geometry</p>
                    <span class="mt-2 inline-block px-2 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800 font-bold">Completed</span>
                </div>

                <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Period 2 (09:45 - 10:30)</span>
                    <h4 class="font-bold text-slate-900 dark:text-white mt-1">Class 9 - Section B</h4>
                    <p class="text-slate-500 mt-0.5">Trigonometry</p>
                    <span class="mt-2 inline-block px-2 py-0.5 rounded text-[10px] bg-amber-100 text-amber-800 font-bold">Next</span>
                </div>

                <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Period 4 (11:30 - 12:15)</span>
                    <h4 class="font-bold text-slate-900 dark:text-white mt-1">Class 10 - Section A</h4>
                    <p class="text-slate-500 mt-0.5">Quadratic Equations</p>
                </div>

                <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Period 6 (01:45 - 02:30)</span>
                    <h4 class="font-bold text-slate-900 dark:text-white mt-1">Class 7 - Section A</h4>
                    <p class="text-slate-500 mt-0.5">Fractions & Decimals</p>
                </div>

                <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Period 7 (02:30 - 03:15)</span>
                    <h4 class="font-bold text-slate-900 dark:text-white mt-1">Class 8 - Section B</h4>
                    <p class="text-slate-500 mt-0.5">Remedial Math</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
