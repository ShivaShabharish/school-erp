<x-layouts.app>
    <x-slot:title>{{ $class->name }} ({{ $section->name }}) - Student Roster</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.classes.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Classes Overview
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $class->name }} - {{ $section->name }}</span>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.students.create') }}"
                    class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Admit to this Section</span>
                </a>
            </div>
        </div>

        <!-- Class Banner & Metrics -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 md:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">
                        Tamil Nadu State Board Curriculum
                    </span>
                    <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1">
                        {{ $class->name }} — Section {{ $section->name }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Assigned Room: <strong>Room {{ $section->room_no ?: '101' }}</strong> • Max Capacity: {{ $section->capacity ?: 40 }} seats
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center min-w-[120px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Class Strength</span>
                        <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-0.5 block">
                            {{ $totalStrength }}
                        </span>
                        <span class="text-[10px] text-slate-500">Students enrolled</span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center min-w-[120px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Today's Attendance</span>
                        <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                            {{ $attendancePct }}%
                        </span>
                        <span class="text-[10px] text-slate-500">{{ $presentCount }} present today</span>
                    </div>
                </div>
            </div>

            <!-- Student Roster Table -->
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Roll No</th>
                            <th class="px-5 py-3">Admission No</th>
                            <th class="px-5 py-3">Student Full Name</th>
                            <th class="px-5 py-3">Gender</th>
                            <th class="px-5 py-3">Blood Group</th>
                            <th class="px-5 py-3">Parent Name & Phone</th>
                            <th class="px-5 py-3 text-right">360° Profile</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @forelse($students as $st)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $st->roll_number ?: '-' }}
                                </td>
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $st->admission_no }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $st->full_name }}
                                </td>
                                <td class="px-5 py-3.5 capitalize">
                                    {{ $st->gender }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-rose-600 dark:text-rose-400">
                                    {{ $st->blood_group ?: 'B+' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    {{ $st->father_name }}
                                    <span class="block text-[10px] font-mono text-slate-400">{{ $st->parent_phone }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a
                                        href="{{ route('admin.students.show', $st) }}"
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-[11px] font-bold transition-all border border-slate-300 dark:border-slate-600 inline-flex items-center gap-1"
                                    >
                                        <span>View 360°</span>
                                        <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    No students are enrolled in {{ $class->name }} - Section {{ $section->name }} yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
