<x-layouts.app>
    <x-slot:title>Marks Entry - {{ $schedule->exam?->name }} ({{ $schedule->subject?->name }})</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.academics.exams.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Examinations Roster
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Marks Sheet</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                    Max Marks: {{ $schedule->max_marks }} &bull; Pass Cutoff: {{ $schedule->pass_marks }}
                </span>
            </div>
        </div>

        <!-- Sheet Header Banner -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">
                    {{ $schedule->exam?->name }}
                </span>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1">
                    {{ $schedule->schoolClass?->name }} &bull; {{ $schedule->subject?->name }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Tamil Nadu CCE Grading Scale (A1: 91-100, A2: 81-90, B1: 71-80, B2: 61-70, C1: 51-60, C2: 41-50, D: 33-40, E: &lt;33)
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center min-w-[120px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Class Cohort</span>
                    <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-0.5 block">
                        {{ $students->count() }}
                    </span>
                    <span class="text-[10px] text-slate-500">Enrolled Students</span>
                </div>
            </div>
        </div>

        <!-- Marks Entry Form -->
        <form action="{{ route('admin.academics.exams.marks.save', $schedule) }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/40">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Student Evaluation Roster
                    </span>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save Marks & Update Grades</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-3">Roll No</th>
                                <th class="px-5 py-3">Admission No</th>
                                <th class="px-5 py-3">Student Name</th>
                                <th class="px-5 py-3">Marks Obtained (Max: {{ $schedule->max_marks }})</th>
                                <th class="px-5 py-3">Absent?</th>
                                <th class="px-5 py-3">CCE Grade</th>
                                <th class="px-5 py-3 text-right">Report Card</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            @foreach($students as $st)
                                @php
                                    $existing = $existingMarks->get($st->id);
                                    $marksVal = $existing ? $existing->marks_obtained : '';
                                    $grade = $existing ? $existing->grade : '-';
                                    $isAbsent = $existing ? $existing->is_absent : false;
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                    <td class="px-5 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $st->roll_number ?: '-' }}
                                    </td>
                                    <td class="px-5 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $st->admission_no }}
                                    </td>
                                    <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $st->full_name }}
                                        <span class="block text-[10px] text-slate-400 font-normal">Section {{ $st->section?->name }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <input
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            max="{{ $schedule->max_marks }}"
                                            name="marks[{{ $st->id }}][marks_obtained]"
                                            value="{{ old("marks.{$st->id}.marks_obtained", $marksVal) }}"
                                            placeholder="Score"
                                            class="w-24 px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono font-bold text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </td>
                                    <td class="px-5 py-3">
                                        <label class="inline-flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="marks[{{ $st->id }}][is_absent]"
                                                value="1"
                                                {{ $isAbsent ? 'checked' : '' }}
                                                class="h-4 w-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300"
                                            />
                                            <span>Absent</span>
                                        </label>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="px-2.5 py-1 rounded text-xs font-extrabold uppercase {{ in_array($grade, ['A1', 'A2', 'B1']) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                            {{ $grade }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a
                                            href="{{ route('admin.academics.exams.report-card', [$schedule->exam, $st]) }}"
                                            class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-[11px] transition-colors inline-flex items-center gap-1"
                                            title="Download Report Card PDF"
                                        >
                                            <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Report PDF</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-end">
                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save Marks & Update Grades</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
