<!-- Sidebar Container -->
<aside
    class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-100 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col shrink-0 border-r border-slate-800"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    <!-- Brand Crest & Header -->
    <div class="flex h-16 items-center gap-3 px-5 border-b border-slate-800 bg-slate-950/60">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-md font-extrabold font-heading text-lg">
            TS
        </div>
        <div class="flex flex-col">
            <span class="font-heading font-extrabold text-sm tracking-tight text-white leading-tight">Test Subject HSS</span>
            <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-semibold">Tamil Nadu State Board</span>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">

        @if(request()->routeIs('accounts.*'))
            <!-- Dedicated Accounts & Billing Area Navigation -->
            <div>
                <div class="flex items-center justify-between px-3 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Billing & Accounts</span>
                    <span class="px-1.5 py-0.5 text-[9px] font-extrabold bg-amber-500/20 text-amber-300 rounded border border-amber-500/30">FINANCE</span>
                </div>
                <nav class="space-y-1">
                    <a
                        href="{{ route('accounts.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('accounts.dashboard') ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Financial Dashboard
                    </a>

                    <a
                        href="{{ route('accounts.dashboard') }}#fees"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Fee Structures & Dues
                    </a>

                    <a
                        href="{{ route('accounts.dashboard') }}#collection"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Fee Collection & Receipts
                    </a>

                    <a
                        href="{{ route('accounts.dashboard') }}#ledger"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Income & Expense Ledger
                    </a>

                    <a
                        href="{{ route('accounts.dashboard') }}#store"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        School Store & Uniforms
                    </a>

                    <div class="pt-4 border-t border-slate-800 mt-4">
                        @if(auth()->user()->isAdmin())
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-emerald-400 hover:bg-slate-800"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                Back to Main ERP
                            </a>
                        @endif
                    </div>
                </nav>
            </div>

        @elseif(auth()->user()->isAdmin())
            <!-- Admin Navigation (Chairman & Principal) -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Core ERP</span>
                <nav class="space-y-1">
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Executive Dashboard
                    </a>

                    <a
                        href="{{ route('admin.students.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ (request()->routeIs('admin.students.*') || request()->routeIs('admin.admissions.*') || request()->routeIs('admin.classes.*')) ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Students & Admissions
                    </a>

                    <a
                        href="{{ route('admin.staff.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('admin.staff.*') ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Staff & Biometrics
                    </a>

                    <a
                        href="{{ route('admin.dashboard') }}#academics"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Academics & Timetables
                    </a>

                    <a
                        href="{{ route('admin.dashboard') }}#calendar"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Calendar & Records
                    </a>

                    <a
                        href="{{ route('admin.dashboard') }}#settings"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings & Safety
                    </a>
                </nav>
            </div>

        @elseif(auth()->user()->isTeacher() || auth()->user()->role === 'staff')
            <!-- Teacher & Staff Navigation -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Staff Portal</span>
                <nav class="space-y-1">
                    <a
                        href="{{ route('staff.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('staff.dashboard') ? 'bg-emerald-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Teacher Dashboard
                    </a>
                </nav>
            </div>

        @elseif(auth()->user()->isParent())
            <!-- Parent Navigation -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Parent Portal</span>
                <nav class="space-y-1">
                    <a
                        href="{{ route('parent.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('parent.dashboard') ? 'bg-emerald-700 text-white' : 'text-slate-300 hover:bg-slate-800' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Ward Dashboard
                    </a>
                </nav>
            </div>
        @endif

    </div>

    <!-- System Status Bottom Bar -->
    <div class="p-3 border-t border-slate-800 bg-slate-950/40 text-[11px] flex items-center justify-between text-slate-400">
        <span class="flex items-center gap-1.5">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            System Online
        </span>
        <a href="{{ route('health') }}" target="_blank" class="hover:text-emerald-400 font-mono text-[10px]">/health</a>
    </div>
</aside>
