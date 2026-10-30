<x-layouts.app>
    <x-slot:title>Tamil Nadu CCE Examination Schedule</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ newExamModal: false }">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.academics.timetables.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Weekly Timetables
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">CCE Examinations</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Tamil Nadu CCE Examinations & Marks
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Continuous Comprehensive Evaluation framework with Formative (FA) and Summative (SA) assessments.
                </p>
            </div>

            <!-- Action Button -->
            <button
                type="button"
                @click="newExamModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Schedule New Exam</span>
            </button>
        </div>

        <!-- Examinations Grid -->
        <div class="space-y-6">
            @forelse($exams as $exam)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-lg font-extrabold font-heading text-slate-900 dark:text-white">
                                    {{ $exam->name }}
                                </h2>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $exam->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                    {{ $exam->status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Category: <strong class="capitalize">{{ $exam->exam_type }}</strong> &bull;
                                Duration: {{ \Carbon\Carbon::parse($exam->start_date)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono text-slate-500 font-semibold">
                                {{ $exam->schedules->count() }} Schedules Assigned
                            </span>
                        </div>
                    </div>

                    <!-- Exam Schedules Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3">Standard / Class</th>
                                    <th class="px-5 py-3">Subject</th>
                                    <th class="px-5 py-3">Exam Date</th>
                                    <th class="px-5 py-3">Timing</th>
                                    <th class="px-5 py-3">Max Marks</th>
                                    <th class="px-5 py-3">Pass Marks</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                                @forelse($exam->schedules as $sch)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                        <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                            {{ $sch->schoolClass?->name }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $sch->subject?->name }}</span>
                                            <span class="block text-[10px] font-mono text-slate-400">{{ $sch->subject?->code }}</span>
                                        </td>
                                        <td class="px-5 py-3.5 font-semibold">
                                            {{ \Carbon\Carbon::parse($sch->exam_date)->format('D, d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 font-mono text-slate-600 dark:text-slate-400">
                                            {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                        </td>
                                        <td class="px-5 py-3.5 font-mono font-bold">
                                            {{ $sch->max_marks }}
                                        </td>
                                        <td class="px-5 py-3.5 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                            {{ $sch->pass_marks }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <a
                                                href="{{ route('admin.academics.exams.marks.index', $sch) }}"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] shadow-xs inline-flex items-center gap-1 cursor-pointer"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Marks Sheet</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-6 text-center text-slate-400">
                                            No subject schedules assigned to this examination yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-400 text-xs">
                    No academic examinations scheduled yet. Click "Schedule New Exam" above.
                </div>
            @endforelse
        </div>

        <!-- Modal: Schedule New Exam -->
        <div
            x-show="newExamModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="newExamModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Schedule Examination Session
                    </h3>
                    <button type="button" @click="newExamModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.academics.exams.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Examination Title *</label>
                        <input type="text" name="name" required placeholder="e.g. Half-Yearly Examination 2026-2027" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Assessment Type *</label>
                            <select name="exam_type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="quarterly">Quarterly Assessment</option>
                                <option value="half_yearly">Half-Yearly Assessment</option>
                                <option value="annual">Annual Board Exam</option>
                                <option value="mid_term">Mid-Term Assessment</option>
                                <option value="unit_test">Unit Test</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Academic Session *</label>
                            <select name="academic_year_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                @endforeach
                            </select>
                        </div>
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

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="newExamModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Create Examination
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
