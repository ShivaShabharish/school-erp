<x-layouts.app>
    <x-slot:title>Class & Teacher Timetables</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            assignModal: false,
            classes: {{ Js::from($classes) }},
            selectedClass: '{{ $selectedClassId }}',
            get currentSections() {
                const c = this.classes.find(item => item.id == this.selectedClass);
                return c ? c.sections : [];
            }
        }"
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Academic Weekly Timetable Schedule
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    8-period daily curriculum matrix with automated clash detection and conflict avoidance.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.academics.substitutions.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>Smart Substitutions</span>
                </a>

                <a
                    href="{{ route('admin.academics.exams.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>CCE Exams & Marks</span>
                </a>

                <button
                    type="button"
                    @click="assignModal = true"
                    class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Assign Period Slot</span>
                </button>
            </div>
        </div>

        <!-- Class & Section Selector Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <form method="GET" action="{{ route('admin.academics.timetables.index') }}" class="flex flex-wrap items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Standard / Class:</label>
                    <select
                        name="class_id"
                        x-model="selectedClass"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Section:</label>
                    <select
                        name="section_id"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <template x-for="s in currentSections" :key="s.id">
                            <option :value="s.id" :selected="s.id == '{{ $selectedSectionId }}'" x-text="'Section ' + s.name"></option>
                        </template>
                    </select>
                </div>

                <button
                    type="submit"
                    class="px-4 py-1.5 rounded-lg bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold transition-colors cursor-pointer"
                >
                    View Timetable
                </button>
            </form>
        </div>

        <!-- Timetable Grid -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 uppercase font-extrabold text-[10px]">
                            <th class="p-3 w-28 text-left border-r border-slate-200 dark:border-slate-700">Day / Period</th>
                            @foreach($periods as $p)
                                <th class="p-3 border-r border-slate-200 dark:border-slate-700 min-w-[120px]">
                                    <div>Period {{ $p }}</div>
                                    <span class="text-[9px] text-slate-400 font-mono normal-case font-medium">
                                        {{ sprintf('%02d:00', 8 + $p) }} - {{ sprintf('%02d:45', 8 + $p) }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @foreach($days as $day)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                <td class="p-3 font-extrabold font-heading text-left capitalize bg-slate-50/50 dark:bg-slate-900/40 border-r border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                                    {{ $day }}
                                </td>
                                @foreach($periods as $p)
                                    @php
                                        $slotKey = $day . '_' . $p;
                                        $slot = $slots->get($slotKey);
                                    @endphp
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-700 align-top">
                                        @if($slot)
                                            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-left space-y-1 shadow-2xs">
                                                <div class="flex items-center justify-between font-bold text-emerald-900 dark:text-emerald-200 text-xs">
                                                    <span>{{ $slot->subject?->code }}</span>
                                                    <span class="text-[9px] font-mono font-medium text-emerald-700 dark:text-emerald-400">{{ $slot->room_no ?: 'Room 101' }}</span>
                                                </div>
                                                <div class="text-[10px] text-slate-600 dark:text-slate-300 font-semibold truncate" title="{{ $slot->subject?->name }}">
                                                    {{ $slot->subject?->name }}
                                                </div>
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate flex items-center gap-1">
                                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span>{{ $slot->teacher?->user?->name ?? 'Staff' }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-3 text-[10px] text-slate-400 italic">
                                                Free Slot
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal: Assign Period Slot -->
        <div
            x-show="assignModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="assignModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Assign Timetable Period Slot
                    </h3>
                    <button type="button" @click="assignModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.academics.timetables.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
                    <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Day of Week *</label>
                            <select name="day_of_week" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($days as $d)
                                    <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Period Number *</label>
                            <select name="period_number" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($periods as $p)
                                    <option value="{{ $p }}">Period {{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Academic Subject *</label>
                        <select name="subject_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Assigned Teacher (Clash Checked) *</label>
                        <select name="staff_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($staffMembers as $t)
                                <option value="{{ $t->id }}">{{ $t->user?->name }} ({{ $t->employee_code }} • {{ $t->designation }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Allocated Classroom / Lab</label>
                        <input type="text" name="room_no" placeholder="e.g. Room 204 or Physics Lab" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="assignModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Verify & Allocate Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
