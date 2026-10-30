<x-layouts.app>
    <x-slot:title>School Events & Academic Calendar</x-slot:title>
    <x-slot:headerTitle>Communication &bull; School Calendar</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ newEventModal: false }">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Admin Dashboard
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">School Calendar</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Academic Year Calendar & Events
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Official state holidays, sports days, cultural festivals, term exam windows, and PTM schedules.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.communication.announcements.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span>Notice Board</span>
                </a>

                <a
                    href="{{ route('admin.communication.certificates.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Certificates (TC/Bonafide)</span>
                </a>

                <a
                    href="{{ route('admin.communication.id-cards.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <span>ID Cards</span>
                </a>

                <button
                    type="button"
                    @click="newEventModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add Calendar Event</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Total Year Holidays</span>
                <div class="mt-2 text-2xl font-bold font-heading text-rose-600 dark:text-rose-400">
                    {{ $totalHolidaysInYear }} Days
                </div>
                <p class="mt-1 text-xs text-slate-500">Government & institutional holidays</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Functions & Annual Days</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    {{ $totalFunctions }} Scheduled
                </div>
                <p class="mt-1 text-xs text-slate-500">Sports day, cultural fests, science expo</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Current Academic Year</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    {{ $currentYear?->name ?? '2026-2027' }}
                </div>
                <p class="mt-1 text-xs text-slate-500">April 2026 to March 2027 cycle</p>
            </div>
        </div>

        <!-- Month Navigation Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold font-heading text-slate-900 dark:text-white">
                    {{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }}
                </h2>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                    {{ $events->count() }} Events Listed
                </span>
            </div>

            <form method="GET" action="{{ route('admin.communication.calendar.index') }}" class="flex items-center gap-2">
                <select
                    name="month"
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-semibold"
                >
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>

                <select
                    name="year"
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-semibold"
                >
                    <option value="2026" {{ $year === 2026 ? 'selected' : '' }}>2026</option>
                    <option value="2027" {{ $year === 2027 ? 'selected' : '' }}>2027</option>
                </select>

                <button
                    type="submit"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold"
                >
                    Change Month
                </button>
            </form>
        </div>

        <!-- Events List Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Event Title</th>
                            <th class="px-4 py-3">Event Type</th>
                            <th class="px-4 py-3">Target Audience</th>
                            <th class="px-4 py-3">Description / Remarks</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($events as $ev)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $ev->start_date->format('d M Y') }}</span>
                                    @if($ev->end_date && $ev->end_date->gt($ev->start_date))
                                        <span class="text-[10px] text-slate-400">to {{ $ev->end_date->format('d M Y') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $ev->title }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ev->is_holiday ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($ev->event_type === 'exam' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                        {{ $ev->event_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-bold">
                                    {{ $ev->target_audience }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 max-w-sm truncate">
                                    {{ $ev->description ?: '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.communication.calendar.destroy', $ev) }}" onsubmit="return confirm('Remove event?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    No calendar events scheduled for {{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }}. Click "Add Calendar Event" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Event Modal -->
        <div
            x-show="newEventModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newEventModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Add Academic Calendar Event
                    </h3>
                    <button @click="newEventModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.communication.calendar.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Event Title *</label>
                        <input
                            type="text"
                            name="title"
                            required
                            placeholder="e.g. Deepavali Holiday, Annual Sports Meet, Science Expo"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Event Type *</label>
                            <select
                                name="event_type"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                            >
                                <option value="holiday">Official Holiday</option>
                                <option value="function">School Function / Festival</option>
                                <option value="exam">Examination Window</option>
                                <option value="ptm">Parent-Teacher Meeting</option>
                                <option value="sports">Sports & Athletics</option>
                                <option value="academic">Academic Workshop</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Audience *</label>
                            <select
                                name="target_audience"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                            >
                                <option value="all">All School</option>
                                <option value="students">Students Only</option>
                                <option value="staff">Teaching & Staff Only</option>
                                <option value="parents">Parents</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Start Date *</label>
                            <input
                                type="date"
                                name="start_date"
                                required
                                value="{{ now()->toDateString() }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">End Date (Optional)</label>
                            <input
                                type="date"
                                name="end_date"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="is_holiday" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                            <span>Mark as Full School Holiday (Classes suspended)</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description / Guidelines</label>
                        <textarea
                            name="description"
                            rows="2"
                            placeholder="Optional notes or timetable instructions..."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newEventModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
