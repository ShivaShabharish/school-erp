<x-layouts.app>
    <x-slot:title>Daily Student Roll-Call Attendance</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            classes: {{ Js::from($classes) }},
            selectedClass: '{{ $selectedClassId }}',
            get currentSections() {
                const c = this.classes.find(item => item.id == this.selectedClass);
                return c ? c.sections : [];
            },
            markAllPresent() {
                document.querySelectorAll('.status-present-radio').forEach(r => r.checked = true);
            }
        }"
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.classes.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Classes & Sections
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Roll-Call Register</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Daily Section Roll-Call Register
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Morning session classroom roll-call with real-time turnout analytics and parent portal updates.
                </p>
            </div>

            <!-- Selector Form -->
            <form method="GET" action="{{ route('admin.academics.student-attendance.index') }}" class="flex flex-wrap items-center gap-3 text-xs self-start md:self-auto">
                <select
                    name="class_id"
                    x-model="selectedClass"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select
                    name="section_id"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <template x-for="s in currentSections" :key="s.id">
                        <option :value="s.id" :selected="s.id == '{{ $selectedSectionId }}'" x-text="'Section ' + s.name"></option>
                    </template>
                </select>

                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                />

                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold transition-colors cursor-pointer shadow-xs"
                >
                    Load Roster
                </button>
            </form>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Class Strength</span>
                <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1 block">
                    {{ $students->count() }}
                </span>
                <span class="text-[10px] text-slate-500">Enrolled Students</span>
            </div>

            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">Present Today</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $presentCount }}
                </span>
                <span class="text-[10px] text-emerald-700/80">In Classroom</span>
            </div>

            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 block">Absent Today</span>
                <span class="text-2xl font-extrabold font-heading text-rose-600 dark:text-rose-400 mt-1 block">
                    {{ $absentCount }}
                </span>
                <span class="text-[10px] text-rose-700/80">Absence Recorded</span>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs text-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Section Turnout</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $turnoutPct }}%
                </span>
                <span class="text-[10px] text-slate-500">Attendance Ratio</span>
            </div>
        </div>

        <!-- Attendance Roll-Call Form -->
        @if($students->isNotEmpty())
            <form action="{{ route('admin.academics.student-attendance.record') }}" method="POST">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Roll-Call Register: {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="markAllPresent()"
                                class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                            >
                                Quick: Mark All Present
                            </button>

                            <button
                                type="submit"
                                class="px-5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-1.5 cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Save Attendance Register</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3">Roll No</th>
                                    <th class="px-5 py-3">Admission No</th>
                                    <th class="px-5 py-3">Student Full Name</th>
                                    <th class="px-5 py-3">Gender</th>
                                    <th class="px-5 py-3">Status Options</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                                @foreach($students as $st)
                                    @php
                                        $att = $attendances->get($st->id);
                                        $currentStatus = $att ? $att->status : 'present';
                                    @endphp
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
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-4">
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                                    <input
                                                        type="radio"
                                                        name="attendance[{{ $st->id }}]"
                                                        value="present"
                                                        {{ $currentStatus === 'present' ? 'checked' : '' }}
                                                        class="status-present-radio h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                                    />
                                                    <span>Present</span>
                                                </label>

                                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-rose-600 dark:text-rose-400">
                                                    <input
                                                        type="radio"
                                                        name="attendance[{{ $st->id }}]"
                                                        value="absent"
                                                        {{ $currentStatus === 'absent' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-slate-300"
                                                    />
                                                    <span>Absent</span>
                                                </label>

                                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-amber-600 dark:text-amber-400">
                                                    <input
                                                        type="radio"
                                                        name="attendance[{{ $st->id }}]"
                                                        value="late"
                                                        {{ $currentStatus === 'late' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-slate-300"
                                                    />
                                                    <span>Late</span>
                                                </label>
                                            </div>
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
                            <span>Save Attendance Register</span>
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-400 text-xs">
                No students enrolled in this section. Please select a valid class and section above.
            </div>
        @endif
    </div>
</x-layouts.app>
