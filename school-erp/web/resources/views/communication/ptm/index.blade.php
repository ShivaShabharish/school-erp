<x-layouts.app>
    <x-slot:title>Parent-Teacher Meetings (PTM)</x-slot:title>
    <x-slot:headerTitle>Communication &bull; PTM Schedules</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newPtmModal: false,
            classes: {{ Js::from($classes) }},
            selectedClass: '',
            get currentSections() {
                const c = this.classes.find(item => item.id == this.selectedClass);
                return c ? c.sections : [];
            }
        }"
    >
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.communication.calendar.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Calendar
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">PTM Scheduling</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Parent-Teacher Meetings (PTM)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Schedule and coordinate section-wise PTM conferences, agenda notes, and classroom venue allocations.
                </p>
            </div>

            <button
                type="button"
                @click="newPtmModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Schedule New PTM</span>
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- PTM Schedules Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white">
                        Scheduled PTM Sessions
                    </h2>
                    <p class="text-xs text-slate-500">Upcoming meeting windows for parents and class teachers</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                    {{ $upcomingCount }} Upcoming
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Meeting Date</th>
                            <th class="px-4 py-3">Class & Section</th>
                            <th class="px-4 py-3">Time Window</th>
                            <th class="px-4 py-3">Venue / Room</th>
                            <th class="px-4 py-3">Agenda / Discussion Focus</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($schedules as $ptm)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono text-slate-800 dark:text-slate-200 font-bold whitespace-nowrap">
                                    {{ $ptm->meeting_date->format('d M Y (l)') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ $ptm->schoolClass?->name }} - {{ $ptm->section?->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    {{ substr($ptm->start_time, 0, 5) }} - {{ substr($ptm->end_time, 0, 5) }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300 font-semibold">
                                    {{ $ptm->venue }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 max-w-sm">
                                    {{ $ptm->agenda ?: 'Quarterly academic progress and student attendance discussion' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ptm->status === 'completed' ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                        {{ $ptm->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    No PTM sessions scheduled yet. Click "Schedule New PTM" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>

        <!-- Schedule PTM Modal -->
        <div
            x-show="newPtmModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newPtmModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Schedule Parent-Teacher Meeting (PTM)
                    </h3>
                    <button @click="newPtmModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.communication.ptm.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Class *</label>
                            <select
                                name="school_class_id"
                                x-model="selectedClass"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="">-- Choose Class --</option>
                                <template x-for="cl in classes" :key="cl.id">
                                    <option :value="cl.id" x-text="cl.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Section *</label>
                            <select
                                name="section_id"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="">-- Choose Section --</option>
                                <template x-for="sec in currentSections" :key="sec.id">
                                    <option :value="sec.id" x-text="sec.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Date *</label>
                            <input
                                type="date"
                                name="meeting_date"
                                required
                                value="{{ now()->addDays(7)->toDateString() }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Start Time *</label>
                            <input
                                type="time"
                                name="start_time"
                                required
                                value="09:30"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">End Time *</label>
                            <input
                                type="time"
                                name="end_time"
                                required
                                value="13:00"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Venue / Room *</label>
                        <input
                            type="text"
                            name="venue"
                            required
                            value="Classroom / School Auditorium"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Agenda / Focus Points</label>
                        <textarea
                            name="agenda"
                            rows="2"
                            placeholder="e.g. Quarterly Exam report cards, behavioral notes, board exam preparation"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newPtmModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Save & Notify Parents
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
