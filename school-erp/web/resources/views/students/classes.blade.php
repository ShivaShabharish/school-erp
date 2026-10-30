<x-layouts.app>
    <x-slot:title>Class Structure & Sections Roster</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div class="space-y-6">
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
                    <span class="text-slate-800 dark:text-slate-200">Classes & Sections</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Academic Classes & Section Roster
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Browse Tamil Nadu State Board standard divisions from LKG through Grade 12 with room allocations and capacity metrics.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.students.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>New Admission</span>
                </a>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center gap-4">
                <div class="p-3 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Standards</span>
                    <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white">{{ $classes->count() }} Classes</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center gap-4">
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Sections</span>
                    <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white">{{ $totalSections }} Sections</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center gap-4">
                <div class="p-3 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Active Strength</span>
                    <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white">{{ $totalStrength }} Students</span>
                </div>
            </div>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($classes as $c)
                @php
                    $classStrength = $c->sections->sum('students_count');
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4 hover:border-emerald-500/50 transition-colors">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">
                                CODE: {{ $c->code }}
                            </span>
                            <h3 class="text-lg font-extrabold font-heading text-slate-900 dark:text-white">
                                {{ $c->name }}
                            </h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            {{ $classStrength }} Enrolled
                        </span>
                    </div>

                    <!-- Sections Roster for this Class -->
                    <div class="space-y-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Class Sections</span>
                        @foreach($c->sections as $sec)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 dark:text-white">Section {{ $sec->name }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">Room {{ $sec->room_no ?: '101' }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500">
                                        Strength: <strong>{{ $sec->students_count }}</strong> / {{ $sec->capacity ?: 40 }} students
                                    </span>
                                </div>
                                <a
                                    href="{{ route('admin.classes.show', [$c, $sec]) }}"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] transition-all shadow-xs"
                                >
                                    Roster →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
