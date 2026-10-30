<x-layouts.app>
    <x-slot:title>Batch Student Promotion Wizard</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            selectedAll: false,
            classes: {{ Js::from($classes) }},
            toClassId: '',
            get toSections() {
                const c = this.classes.find(item => item.id == this.toClassId);
                return c ? c.sections : [];
            },
            toggleAll(e) {
                const checked = e.target.checked;
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
            }
        }"
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.students.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Student Directory
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Promotion Wizard</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Academic Year Batch Promotion
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Seamlessly transition an entire class section to the next academic grade with automated audit logs.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs font-bold text-amber-800 dark:text-amber-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Current Session: {{ $currentYear?->name ?? '2026-2027' }}</span>
            </div>
        </div>

        <!-- Wizard Step 1: Class Selection Filter Form -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <h2 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                Step 1: Select Source Class & Section to Promote
            </h2>

            <form
                action="{{ route('admin.students.promotion') }}"
                method="GET"
                class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                x-data="{
                    fromClassId: '{{ $selectedClassId ?: ($classes->first()->id ?? '') }}',
                    classes: {{ Js::from($classes) }},
                    get fromSections() {
                        const c = this.classes.find(item => item.id == this.fromClassId);
                        return c ? c.sections : [];
                    }
                }"
            >
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Source Class *</label>
                    <select
                        name="from_class_id"
                        x-model="fromClassId"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Source Section *</label>
                    <select
                        name="from_section_id"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <template x-for="s in fromSections" :key="s.id">
                            <option :value="s.id" :selected="s.id == '{{ $selectedSectionId }}'" x-text="s.name"></option>
                        </template>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full py-2.5 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Load Enrolled Students</span>
                    </button>
                </div>
            </form>
        </div>

        @if($students->isNotEmpty())
            <!-- Wizard Step 2 & 3: Target Class and Student Roster Selection -->
            <form action="{{ route('admin.students.promotion.promote') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="from_class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="from_section_id" value="{{ $selectedSectionId }}">

                <!-- Destination Setup -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs space-y-4">
                    <h2 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                        Step 2: Choose Destination Target Class & Academic Session
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Destination Class *</label>
                            <select
                                name="to_class_id"
                                x-model="toClassId"
                                required
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">Select Target Class</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Destination Section *</label>
                            <select
                                name="to_section_id"
                                required
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <template x-for="s in toSections" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Target Academic Year *</label>
                            <select
                                name="academic_year_id"
                                required
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Student List with Checkboxes -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="h-6 w-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                            <div>
                                <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                                    Step 3: Verify & Select Eligible Candidates
                                </h3>
                                <p class="text-[11px] text-slate-400">{{ $students->count() }} active students found in this roster</p>
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input
                                type="checkbox"
                                @change="toggleAll($event)"
                                class="h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                            />
                            <span>Select All Students</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-left text-xs">
                            <thead class="sticky top-0 bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3 w-10">Select</th>
                                    <th class="px-5 py-3">Roll No</th>
                                    <th class="px-5 py-3">Admission No</th>
                                    <th class="px-5 py-3">Student Name</th>
                                    <th class="px-5 py-3">Parent Name & Contact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                                @foreach($students as $st)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40">
                                        <td class="px-5 py-3">
                                            <input
                                                type="checkbox"
                                                name="student_ids[]"
                                                value="{{ $st->id }}"
                                                class="student-checkbox h-4 w-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                            />
                                        </td>
                                        <td class="px-5 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                            {{ $st->roll_number ?: '-' }}
                                        </td>
                                        <td class="px-5 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $st->admission_no }}
                                        </td>
                                        <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                            {{ $st->full_name }}
                                        </td>
                                        <td class="px-5 py-3">
                                            {{ $st->father_name }} ({{ $st->parent_phone }})
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-between">
                        <p class="text-xs text-slate-500">
                            Promoting updates the student's active class & section records and logs an immutable transition history.
                        </p>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>Execute Batch Promotion</span>
                        </button>
                    </div>
                </div>
            </form>
        @elseif($selectedClassId && $selectedSectionId)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-400 text-xs">
                No active students currently enrolled in the selected class and section.
            </div>
        @endif

        <!-- Promotion History Log -->
        @if($recentPromotions->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                        Recent Batch Promotion History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3">Student</th>
                                <th class="px-5 py-3">From Class</th>
                                <th class="px-5 py-3">Promoted To</th>
                                <th class="px-5 py-3">Academic Session</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            @foreach($recentPromotions as $rp)
                                <tr>
                                    <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $rp->student?->full_name }}
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $rp->student?->admission_no }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        {{ $rp->fromClass?->name }} ({{ $rp->fromSection?->name }})
                                    </td>
                                    <td class="px-5 py-3 font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $rp->toClass?->name }} ({{ $rp->toSection?->name }})
                                    </td>
                                    <td class="px-5 py-3 font-semibold">
                                        {{ $rp->academicYear?->name }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-400 text-[11px]">
                                        {{ $rp->created_at->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
