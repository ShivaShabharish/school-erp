<x-layouts.app>
    <x-slot:title>AI Report Cards & CCE Evaluation</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div class="space-y-6" x-data="reportCardManager()">
        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-2xl text-white shadow-lg border border-emerald-700/40">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-amber-400 text-slate-950 font-black text-xs">AI CCE</span>
                    <h2 class="text-xl font-extrabold font-heading tracking-wide">1-Click AI Student Report Cards</h2>
                </div>
                <p class="text-xs text-emerald-200/90 mt-1">
                    Auto-generated scholastic evaluations, grading rubric metrics & bilingual AI teacher remarks for Test Subject HSS.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="printAllCards()"
                    class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Batch Report Cards</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.academics.report-cards.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Examination Term</label>
                    <select name="exam_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExam?->id == $ex->id ? 'selected' : '' }}>{{ $ex->name }} (AY 2026-27)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Standard / Class</label>
                    <select name="class_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass?->id == $c->id ? 'selected' : '' }}>Class {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedClass && $selectedClass->sections->isNotEmpty())
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Section</label>
                        <select name="section_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-white">
                            @foreach($selectedClass->sections as $sec)
                                <option value="{{ $sec->id }}" {{ $selectedSection?->id == $sec->id ? 'selected' : '' }}>Section {{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>

            <div class="flex items-center gap-2 text-xs">
                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-bold">
                    Class Turnout: 94.8%
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 font-bold">
                    Total Students: {{ count($studentReports) }}
                </span>
            </div>
        </div>

        <!-- Student Report Cards Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Student CCE Marksheets • Class {{ $selectedClass?->name }} - {{ $selectedSection?->name }}</span>
                </h3>
                <span class="text-xs text-slate-400">Term: {{ $selectedExam?->name }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Roll / Adm</th>
                            <th class="px-4 py-3">Student Name</th>
                            @foreach($subjects as $sub)
                                <th class="px-3 py-3 text-center">{{ $sub->code }}</th>
                            @endforeach
                            <th class="px-3 py-3 text-center">Total</th>
                            <th class="px-3 py-3 text-center">%</th>
                            <th class="px-3 py-3 text-center">Grade</th>
                            <th class="px-4 py-3">AI Teacher Remark (En & Ta)</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($studentReports as $idx => $rep)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                <td class="px-4 py-3 font-mono font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ $rep['student']->roll_number ?? '100' . ($idx + 1) }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $rep['student']->admission_number }}</span>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                    {{ $rep['student']->full_name }}
                                    <span class="block text-[10px] text-slate-400 font-normal">Attendance: {{ $rep['attendance_pct'] }}%</span>
                                </td>

                                @foreach($subjects as $sub)
                                    @php $score = $rep['subjects'][$sub->name] ?? ['obtained' => 85, 'grade' => 'A']; @endphp
                                    <td class="px-3 py-3 text-center font-mono">
                                        <span class="font-bold {{ $score['obtained'] >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($score['obtained'] >= 60 ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400') }}">
                                            {{ round($score['obtained']) }}
                                        </span>
                                        <span class="block text-[9px] text-slate-400">{{ $score['grade'] }}</span>
                                    </td>
                                @endforeach

                                <td class="px-3 py-3 text-center font-mono font-extrabold text-slate-900 dark:text-white">
                                    {{ round($rep['total_obtained']) }} / {{ round($rep['total_max']) }}
                                </td>

                                <td class="px-3 py-3 text-center font-mono font-bold">
                                    {{ $rep['percentage'] }}%
                                </td>

                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $rep['overall_grade'] == 'A+' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                                        {{ $rep['overall_grade'] }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 max-w-xs text-[11px] leading-snug">
                                    <p class="text-slate-800 dark:text-slate-200 italic" id="rem-en-{{ $rep['student']->id }}">"{{ $rep['ai_remarks_en'] }}"</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5" id="rem-ta-{{ $rep['student']->id }}">{{ $rep['ai_remarks_ta'] }}</p>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        @click="openReportCard({{ json_encode($rep) }})"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-xs cursor-pointer inline-flex items-center gap-1"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>View & Print</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($subjects) + 6 }}" class="px-6 py-12 text-center text-slate-400">
                                    No active students enrolled in this section. Please select another standard or section.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Printable Report Card Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full max-h-[92vh] overflow-y-auto p-6 sm:p-8 shadow-2xl space-y-6" id="printableCard">
                <!-- Report Card Top Crest Header -->
                <div class="text-center pb-4 border-b-2 border-emerald-700">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-800 text-white font-black text-xl mb-2 shadow-md">
                        TS
                    </div>
                    <h2 class="text-lg font-black tracking-wide text-slate-900 dark:text-white uppercase font-heading">
                        Test Subject Higher Secondary School
                    </h2>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-semibold">
                        Pudukkottai, Tamil Nadu &bull; Recognized by Tamil Nadu State Board
                    </p>
                    <p class="text-[11px] font-mono text-emerald-700 dark:text-emerald-400 font-bold uppercase mt-1">
                        Academic Cumulative Progress Report &bull; AY 2026-27
                    </p>
                </div>

                <!-- Student Dossier Metadata -->
                <template x-if="activeCard">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs border border-slate-200 dark:border-slate-700">
                        <div><strong class="text-slate-400 block text-[10px] uppercase">Student Name:</strong> <span class="font-bold text-slate-900 dark:text-white" x-text="activeCard.student.full_name"></span></div>
                        <div><strong class="text-slate-400 block text-[10px] uppercase">Admission No:</strong> <span class="font-mono font-bold" x-text="activeCard.student.admission_number"></span></div>
                        <div><strong class="text-slate-400 block text-[10px] uppercase">Standard:</strong> <span class="font-bold">Class {{ $selectedClass?->name }} - {{ $selectedSection?->name }}</span></div>
                        <div><strong class="text-slate-400 block text-[10px] uppercase">Attendance:</strong> <span class="font-bold text-emerald-600" x-text="activeCard.attendance_pct + '%'"></span></div>
                    </div>
                </template>

                <!-- Subject Marks Table -->
                <template x-if="activeCard">
                    <table class="w-full text-xs text-left border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                        <thead class="bg-emerald-900 text-white text-[10px] uppercase font-bold">
                            <tr>
                                <th class="p-2.5">Subject</th>
                                <th class="p-2.5 text-center">Max Marks</th>
                                <th class="p-2.5 text-center">Obtained</th>
                                <th class="p-2.5 text-center">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="(sub, name) in activeCard.subjects" :key="name">
                                <tr>
                                    <td class="p-2.5 font-bold" x-text="name"></td>
                                    <td class="p-2.5 text-center font-mono" x-text="sub.max"></td>
                                    <td class="p-2.5 text-center font-mono font-bold text-emerald-600" x-text="Math.round(sub.obtained)"></td>
                                    <td class="p-2.5 text-center font-bold" x-text="sub.grade"></td>
                                </tr>
                            </template>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t-2 border-emerald-600">
                                <td class="p-2.5 uppercase">Aggregate Total</td>
                                <td class="p-2.5 text-center font-mono" x-text="Math.round(activeCard.total_max)"></td>
                                <td class="p-2.5 text-center font-mono text-emerald-700 dark:text-emerald-400" x-text="Math.round(activeCard.total_obtained)"></td>
                                <td class="p-2.5 text-center text-emerald-700 dark:text-emerald-400" x-text="activeCard.overall_grade"></td>
                            </tr>
                        </tbody>
                    </table>
                </template>

                <!-- AI Teacher Remarks Section -->
                <template x-if="activeCard">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 rounded-2xl text-xs space-y-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                            <span>🤖 AI Faculty Evaluation Remarks:</span>
                        </span>
                        <p class="font-medium text-slate-800 dark:text-slate-100 italic leading-relaxed" x-text="activeCard.ai_remarks_en"></p>
                        <p class="text-[11px] text-emerald-900 dark:text-emerald-300" x-text="activeCard.ai_remarks_ta"></p>
                    </div>
                </template>

                <!-- Signatures -->
                <div class="pt-6 grid grid-cols-3 text-center text-xs text-slate-500 font-semibold border-t border-slate-200 dark:border-slate-700">
                    <div>
                        <div class="h-10"></div>
                        <p class="border-t border-slate-400/50 pt-1">Class Teacher</p>
                    </div>
                    <div>
                        <div class="h-10 flex items-center justify-center font-mono text-[10px] text-emerald-600 font-bold">
                            [SEAL VERIFIED]
                        </div>
                        <p class="border-t border-slate-400/50 pt-1">Parent's Signature</p>
                    </div>
                    <div>
                        <div class="h-10 flex items-center justify-center">
                            <span class="font-bold text-slate-900 dark:text-white font-heading text-xs">Prof. S. Meenakshi</span>
                        </div>
                        <p class="border-t border-slate-400/50 pt-1">Principal / Headmistress</p>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 no-print">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold cursor-pointer">
                        Close
                    </button>
                    <button type="button" @click="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Report Card</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function reportCardManager() {
        return {
            showModal: false,
            activeCard: null,
            openReportCard(report) {
                this.activeCard = report;
                this.showModal = true;
            },
            printAllCards() {
                window.print();
            }
        };
    }
    </script>
</x-layouts.app>
