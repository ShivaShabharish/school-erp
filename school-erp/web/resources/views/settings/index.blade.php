<x-layouts.app>
    <x-slot:title>Settings, Safety & API Gateway</x-slot:title>
    <x-slot:headerTitle>Administration &bull; Settings</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ addYearModal: false }">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Dashboard
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">System Configuration</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Settings, Safety & API Gateway
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Institution profile, academic year activation, audit logs, backup tools, and mobile app integration gateway.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.settings.audit-logs') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Audit Trail ({{ number_format($stats['total_audit_logs']) }})
                </a>

                <a
                    href="{{ route('admin.settings.backup.sql') }}"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download SQL Backup
                </a>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-700 overflow-x-auto text-xs font-bold">
            <a href="{{ route('admin.settings.index', ['tab' => 'profile']) }}"
               class="px-4 py-3 border-b-2 {{ $currentTab === 'profile' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                School Profile & Identity
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'academic_years']) }}"
               class="px-4 py-3 border-b-2 {{ $currentTab === 'academic_years' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Academic Years ({{ $academicYears->count() }})
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'safety_api']) }}"
               class="px-4 py-3 border-b-2 {{ $currentTab === 'safety_api' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800' }} flex items-center gap-2">
                Mobile API Gateway
                @if($stats['app_integration_enabled'])
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                @else
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                @endif
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'roles']) }}"
               class="px-4 py-3 border-b-2 {{ $currentTab === 'roles' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Roles & Permission Matrix
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'backup']) }}"
               class="px-4 py-3 border-b-2 {{ $currentTab === 'backup' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Data Export & Archival
            </a>
        </div>

        {{-- TAB 1: School Profile --}}
        @if($currentTab === 'profile')
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                <form action="{{ route('admin.settings.update-profile') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Institution Legal Identity & Affiliation</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Details printed on CCE progress report cards, payslips, official Bonafide certificates, and Transfer Certificates.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">School Full Name</label>
                            <input type="text" name="school_name" value="{{ old('school_name', $settings['school_name'] ?? 'Test Subject Higher Secondary School') }}"
                                   required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Educational Board</label>
                            <input type="text" name="school_board" value="{{ old('school_board', $settings['school_board'] ?? 'Tamil Nadu State Board') }}"
                                   required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Board Affiliation / Recognition No</label>
                            <input type="text" name="school_affiliation_no" value="{{ old('school_affiliation_no', $settings['school_affiliation_no'] ?? 'TN-EDU-2026-4821') }}"
                                   required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Location & City</label>
                            <input type="text" name="school_location" value="{{ old('school_location', $settings['school_location'] ?? 'Pudukkottai, Tamil Nadu') }}"
                                   required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '+91 4322 221234') }}"
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Administrative Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'info@testsubjecthss.edu.in') }}"
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-100 dark:border-slate-700">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Billing & Security Coordinates</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-4">
                            Receipt numbering sequences and biometric machine encryption keys.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '₹') }}"
                                       required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Fee Receipt Prefix</label>
                                <input type="text" name="receipt_prefix_fees" value="{{ old('receipt_prefix_fees', $settings['receipt_prefix_fees'] ?? 'SCH/2026-27/REC-') }}"
                                       required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Store POS Receipt Prefix</label>
                                <input type="text" name="receipt_prefix_store" value="{{ old('receipt_prefix_store', $settings['receipt_prefix_store'] ?? 'SCH/2026-27/STR-') }}"
                                       required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Biometric Machine API Secret</label>
                                <input type="text" name="biometric_api_secret" value="{{ old('biometric_api_secret', $settings['biometric_api_secret'] ?? 'BIO-SEC-9842-TEST-KEY') }}"
                                       required class="w-full text-xs font-mono font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Settings Changes
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- TAB 2: Academic Years --}}
        @if($currentTab === 'academic_years')
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Academic Years Registry</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Switching the active academic year will dynamically filter student rolls, fee ledgers, and attendance registers.
                        </p>
                    </div>
                    <button @click="addYearModal = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New Academic Year
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($academicYears as $year)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 {{ $year->is_current ? 'border-emerald-600 shadow-md' : 'border-slate-200 dark:border-slate-700' }} p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-extrabold font-heading text-slate-900 dark:text-white">{{ $year->name }}</span>
                                    @if($year->is_current)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            ACTIVE SESSION
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                            Archived
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-1.5 text-slate-600 dark:text-slate-400">
                                    <div class="flex justify-between">
                                        <span>Start Date:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($year->start_date)->format('d M, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>End Date:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($year->end_date)->format('d M, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                @if(! $year->is_current)
                                    <form action="{{ route('admin.settings.switch-year', $year) }}" method="POST" onsubmit="return confirm('Switch active academic year to {{ $year->name }}?')">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 text-xs font-bold transition">
                                            Switch to this Year
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Currently Operating
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Modal: Add Year --}}
            <div x-show="addYearModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div @click.away="addYearModal = false" class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Create New Academic Year</h3>
                    <form action="{{ route('admin.settings.store-year') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Session Name (YYYY-YYYY)</label>
                            <input type="text" name="name" placeholder="2027-2028" required pattern="\d{4}-\d{4}"
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
                            <input type="date" name="start_date" required
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">End Date</label>
                            <input type="date" name="end_date" required
                                   class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900">
                        </div>

                        <div class="flex justify-end gap-3 pt-3">
                            <button type="button" @click="addYearModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                                Create Academic Year
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- TAB 3: Mobile API Gateway --}}
        @if($currentTab === 'safety_api')
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Mobile App Integration Switch</h2>
                            @if($stats['app_integration_enabled'])
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300">
                                    GATEWAY ACTIVE
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300">
                                    DISABLED (HTTP 503)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                            When disabled, all external mobile client requests to <code class="bg-slate-100 dark:bg-slate-900 px-1 py-0.5 rounded text-[11px]">/api/v1/*</code> are safely intercepted. Enable this when deploying or testing the Expo React Native app.
                        </p>
                    </div>

                    <form action="{{ route('admin.settings.toggle-mobile-app') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 {{ $stats['app_integration_enabled'] ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                            @if($stats['app_integration_enabled'])
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Disable Mobile API
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Enable Mobile API
                            @endif
                        </button>
                    </form>
                </div>

                {{-- API Specs Quick Reference --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Sanctum REST API Specification (for Expo Mobile App)</h3>
                    <div class="overflow-x-auto text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="p-2.5">Endpoint</th>
                                    <th class="p-2.5">Method</th>
                                    <th class="p-2.5">Auth Required</th>
                                    <th class="p-2.5">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]">
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/auth/token</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">POST</span></td>
                                    <td class="p-2.5 text-slate-400">None</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Issue Sanctum Bearer token with email & password</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/auth/user</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Authenticated profile & roles</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/student/profile</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">360 biodata, class, section & parent coordinates</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/student/attendance</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Attendance percentage and 30-day log history</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/student/fees</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Tuition dues, 3-term installments & paid receipts</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/student/timetable</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Weekly 6-day period schedule</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/student/homework</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Active assignments with due dates</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 text-emerald-600 font-bold">/api/v1/announcements</td>
                                    <td class="p-2.5"><span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">GET</span></td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Bearer</td>
                                    <td class="p-2.5 font-sans text-slate-600 dark:text-slate-300">Broadcast circulars & pinned notifications</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB 4: Roles & Permissions --}}
        @if($currentTab === 'roles')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($rolesMatrix as $roleKey => $meta)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-3 h-3 rounded-full bg-{{ $meta['color'] }}-500"></span>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $meta['name'] }}</h3>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                                {{ $meta['description'] }}
                            </p>

                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Granted Privileges:</span>
                                @foreach($meta['permissions'] as $perm)
                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>{{ $perm }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center text-[11px] text-slate-500">
                            <span>Role Key: <code class="font-mono text-slate-800 dark:text-slate-200">{{ $roleKey }}</code></span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-900 font-bold">Standard Matrix</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- TAB 5: Backup & Export Center --}}
        @if($currentTab === 'backup')
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Entity-Wise CSV Data Exports</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                        Stream clean, UTF-8 formatted CSV spreadsheets suitable for statutory reporting or Excel analysis.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.settings.export', ['type' => 'students']) }}"
                           class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-600 bg-slate-50 dark:bg-slate-900 transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Students Roster CSV</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">308 enrolled pupil records</div>
                            </div>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>

                        <a href="{{ route('admin.settings.export', ['type' => 'staff']) }}"
                           class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-600 bg-slate-50 dark:bg-slate-900 transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Staff Directory CSV</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Teaching & non-teaching staff</div>
                            </div>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>

                        <a href="{{ route('admin.settings.export', ['type' => 'fee_receipts']) }}"
                           class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-600 bg-slate-50 dark:bg-slate-900 transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Fee Collections CSV</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Receipt ledger & transaction logs</div>
                            </div>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>

                        <a href="{{ route('admin.settings.export', ['type' => 'expenses']) }}"
                           class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-600 bg-slate-50 dark:bg-slate-900 transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Expense Ledger CSV</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Categorized vouchers & payouts</div>
                            </div>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>

                        <a href="{{ route('admin.settings.export', ['type' => 'store_items']) }}"
                           class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-emerald-600 bg-slate-50 dark:bg-slate-900 transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900 dark:text-white">Store Inventory CSV</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Uniforms, books & stock levels</div>
                            </div>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                </div>

                {{-- SQL Database Dump --}}
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-base font-bold">Complete MySQL Database Snapshot (.SQL)</h3>
                        <p class="text-xs text-slate-300 mt-1 max-w-xl">
                            Exports full schema definitions and INSERT statements for all tables with foreign-key integrity flags disabled during restore.
                        </p>
                    </div>
                    <a href="{{ route('admin.settings.backup.sql') }}"
                       class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 whitespace-nowrap shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Database .SQL Dump
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
