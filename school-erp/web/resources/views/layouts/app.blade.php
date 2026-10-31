<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': $store.darkMode.on }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Test Subject Higher Secondary School ERP</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen antialiased flex flex-col font-sans transition-colors duration-200" x-data="{ sidebarOpen: false, searchOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Column -->
        <div class="flex flex-col flex-1 overflow-y-auto">
            <!-- Top Navigation Bar -->
            <header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 sm:px-6">
                <!-- Left: Mobile Toggle & Breadcrumbs -->
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden cursor-pointer"
                        aria-label="Toggle Navigation"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-700 dark:text-emerald-400 font-semibold font-heading">Test Subject HSS</span>
                        <span>/</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $headerTitle ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <!-- Right: Top-Right "Accounts & Billing" Button, Theme Toggle, & Profile Menu -->
                <div class="flex items-center gap-3">
                    <!-- Mandatory Persistent TOP-RIGHT "Accounts & Billing" Portal Button -->
                    @if(auth()->check() && in_array(auth()->user()->role, ['chairman', 'principal', 'accountant']))
                        <a
                            href="{{ route('accounts.dashboard') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold tracking-wide uppercase shadow-xs transition-all cursor-pointer border {{ request()->routeIs('accounts.*') ? 'bg-amber-600 text-white border-amber-600 ring-2 ring-amber-300 dark:ring-amber-900' : 'bg-amber-500 hover:bg-amber-600 text-slate-950 hover:text-white border-amber-400 font-extrabold' }}"
                            title="Open Separate Billing & Accounts Area"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Accounts & Billing</span>
                        </a>
                    @endif

                    <!-- Dark Mode Toggle -->
                    <button
                        type="button"
                        @click="$store.darkMode.toggle()"
                        class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 cursor-pointer transition-colors"
                        title="Toggle Light / Dark Mode"
                        aria-label="Toggle Theme"
                    >
                        <template x-if="$store.darkMode.on">
                            <!-- Sun Icon for switching to light -->
                            <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        <template x-if="!$store.darkMode.on">
                            <!-- Moon Icon for switching to dark -->
                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                    </button>

                    <!-- User Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            @click="open = !open"
                            type="button"
                            class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        >
                            <div class="h-8 w-8 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                            </div>
                            <div class="hidden md:flex flex-col text-left">
                                <span class="text-xs font-bold leading-tight text-slate-900 dark:text-slate-100">{{ auth()->user()->name ?? 'User' }}</span>
                                <span class="text-[10px] font-semibold uppercase text-emerald-700 dark:text-emerald-400">{{ auth()->user()->role ?? 'Role' }}</span>
                            </div>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak
                            class="absolute right-0 mt-2 w-48 rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 text-xs"
                        >
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</p>
                                <p class="text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2 font-semibold cursor-pointer"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 dark:border-slate-800 px-6 py-4 text-center text-xs text-slate-400">
                Test Subject Higher Secondary School &bull; Pudukkottai, Tamil Nadu &bull; ERP v1.0
            </footer>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <x-toast />

    @stack('scripts')
</body>
</html>
