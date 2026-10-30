<x-layouts.app>
    <x-slot:title>Smart Teacher Substitution Engine</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div class="space-y-6">
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
                    <span class="text-slate-800 dark:text-slate-200">Substitution Engine</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Automatic Faculty Substitution Engine
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Detects absent teachers from morning biometric rolls, cross-references scheduled periods, and recommends free qualified substitutes.
                </p>
            </div>

            <!-- Date Picker -->
            <form method="GET" action="{{ route('admin.academics.substitutions.index') }}" class="flex items-center gap-2 self-start md:self-auto">
                <input
                    type="date"
                    name="date"
                    value="{{ $date }}"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs transition-colors cursor-pointer shadow-xs"
                >
                    Analyze Date
                </button>
            </form>
        </div>

        <!-- Substitution Recommendations -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-amber-500 animate-ping"></span>
                <span>Affected Periods & Recommended Substitutes ({{ count($suggestions) }} Slots)</span>
            </h2>

            @forelse($suggestions as $sug)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 uppercase">
                                Period {{ $sug['period'] }} &bull; {{ $sug['class'] }}
                            </span>
                            <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white mt-1.5">
                                {{ $sug['subject'] }}
                            </h3>
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-0.5 flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Absent Primary Faculty: <strong>{{ $sug['absent_teacher'] }}</strong></span>
                            </p>
                        </div>

                        <div>
                            @if($sug['existing_substitution'])
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-xs font-bold border border-emerald-300 dark:border-emerald-800 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Assigned: {{ $sug['existing_substitution']->substituteTeacher?->user?->name }}</span>
                                </span>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-950/40 text-xs font-bold border border-amber-200 dark:border-amber-800">
                                    Substitution Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!$sug['existing_substitution'])
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-3">
                                Available Faculty (Present Today & Free During Period {{ $sug['period'] }})
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @forelse($sug['suggested_candidates'] as $candidate)
                                    <form action="{{ route('admin.academics.substitutions.assign') }}" method="POST" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-between gap-3 hover:border-emerald-500 transition-colors">
                                        @csrf
                                        <input type="hidden" name="timetable_id" value="{{ $sug['timetable']->id }}">
                                        <input type="hidden" name="substitute_staff_id" value="{{ $candidate->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">

                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $candidate->user?->name }}</div>
                                            <span class="text-[10px] text-slate-500 block">{{ $candidate->designation }}</span>
                                            <span class="text-[9px] font-mono text-emerald-600 dark:text-emerald-400">{{ $candidate->department }}</span>
                                        </div>

                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] shadow-xs transition-all cursor-pointer whitespace-nowrap"
                                        >
                                            Assign
                                        </button>
                                    </form>
                                @empty
                                    <div class="col-span-3 text-xs text-slate-400 italic py-2">
                                        No free teachers available during this period slot.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-400 text-xs">
                    All scheduled faculty are present and on-duty for this date. No substitutions required!
                </div>
            @endforelse
        </div>

        <!-- Confirmed Active Substitutions Log -->
        @if($activeSubstitutions->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-extrabold font-heading text-slate-900 dark:text-white uppercase tracking-wider">
                        Active Substitutions Roster for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-5 py-3">Class & Section</th>
                                <th class="px-5 py-3">Period</th>
                                <th class="px-5 py-3">Subject</th>
                                <th class="px-5 py-3">Original Teacher</th>
                                <th class="px-5 py-3">Assigned Substitute</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            @foreach($activeSubstitutions as $asub)
                                <tr>
                                    <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $asub->timetable?->schoolClass?->name }} - {{ $asub->timetable?->section?->name }}
                                    </td>
                                    <td class="px-5 py-3 font-mono font-bold">
                                        Period {{ $asub->timetable?->period_number }}
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $asub->timetable?->subject?->name }}
                                    </td>
                                    <td class="px-5 py-3 text-rose-600 font-semibold">
                                        {{ $asub->originalTeacher?->user?->name }}
                                    </td>
                                    <td class="px-5 py-3 text-emerald-600 font-bold">
                                        {{ $asub->substituteTeacher?->user?->name }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 uppercase">
                                            {{ $asub->status }}
                                        </span>
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
