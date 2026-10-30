<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | Test Subject Higher Secondary School ERP</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans transition-colors duration-200">

    <!-- Top Floating Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button
            type="button"
            @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
            class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors"
            title="Toggle Light / Dark Mode"
        >
            <template x-if="darkMode">
                <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </template>
            <template x-if="!darkMode">
                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </template>
        </button>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- School Crest & Header -->
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-lg shadow-emerald-700/20 font-extrabold font-heading text-2xl tracking-tight">
                TS
            </div>
            <h1 class="mt-4 text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                Test Subject Higher Secondary School
            </h1>
            <p class="mt-1 text-xs font-medium text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">
                Tamil Nadu State Board &bull; Pudukkottai
            </p>
        </div>

        <!-- Main Card -->
        <div class="mt-8 bg-white dark:bg-slate-800 py-8 px-6 shadow-xl rounded-2xl sm:px-10 border border-slate-200 dark:border-slate-700/80">
            <h2 class="text-base font-bold font-heading text-slate-900 dark:text-white mb-5">
                Sign In to Enterprise Portal
            </h2>

            <form
                method="POST"
                action="{{ route('login.post') }}"
                class="space-y-4"
                x-data="{ showPass: false, email: '{{ old('email', 'chairman@school.test') }}', pass: 'Password@123' }"
            >
                @csrf

                <!-- Email Input -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        x-model="email"
                        required
                        autofocus
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-600 transition-colors"
                        placeholder="yourname@school.test"
                    />
                    @error('email')
                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <button
                            type="button"
                            @click="showPass = !showPass"
                            class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 hover:underline cursor-pointer"
                            x-text="showPass ? 'Hide' : 'Show'"
                        ></button>
                    </div>
                    <input
                        :type="showPass ? 'text' : 'password'"
                        name="password"
                        id="password"
                        x-model="pass"
                        required
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-600 transition-colors"
                    />
                    @error('password')
                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-500"
                        />
                        <span class="text-slate-600 dark:text-slate-300 font-medium">Keep me signed in</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Academic Year: 2026-27</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-sm tracking-wide shadow-md shadow-emerald-700/20 transition-all cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>Sign In</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>

                <!-- Quick Demo Login Switcher -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 text-center">
                        One-Click Demo Roles
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button
                            type="button"
                            @click="email = 'chairman@school.test'; pass = 'Password@123'"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-left font-semibold cursor-pointer border border-slate-200 dark:border-slate-600"
                        >
                            <span class="block text-[10px] uppercase text-emerald-700 dark:text-emerald-400 font-bold">Admin</span>
                            Chairman
                        </button>
                        <button
                            type="button"
                            @click="email = 'principal@school.test'; pass = 'Password@123'"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-left font-semibold cursor-pointer border border-slate-200 dark:border-slate-600"
                        >
                            <span class="block text-[10px] uppercase text-emerald-700 dark:text-emerald-400 font-bold">Admin</span>
                            Principal
                        </button>
                        <button
                            type="button"
                            @click="email = 'accountant@school.test'; pass = 'Password@123'"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-amber-50 hover:text-amber-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-left font-semibold cursor-pointer border border-slate-200 dark:border-slate-600 col-span-2"
                        >
                            <span class="block text-[10px] uppercase text-amber-600 dark:text-amber-400 font-bold">Billing Area</span>
                            Accountant (Accounts Portal)
                        </button>
                        <button
                            type="button"
                            @click="email = 'teacher@school.test'; pass = 'Password@123'"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-left font-semibold cursor-pointer border border-slate-200 dark:border-slate-600"
                        >
                            <span class="block text-[10px] uppercase text-slate-400 font-bold">Staff</span>
                            Teacher
                        </button>
                        <button
                            type="button"
                            @click="email = 'parent@school.test'; pass = 'Password@123'"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-left font-semibold cursor-pointer border border-slate-200 dark:border-slate-600"
                        >
                            <span class="block text-[10px] uppercase text-slate-400 font-bold">Family</span>
                            Parent
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Health Status Link -->
        <div class="mt-6 text-center text-xs text-slate-400">
            System Connectivity: <a href="{{ route('health') }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline font-mono">/health (Active)</a>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <x-toast />

</body>
</html>
