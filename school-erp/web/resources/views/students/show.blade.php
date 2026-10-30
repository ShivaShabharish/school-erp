<x-layouts.app>
    <x-slot:title>Student 360° Profile - {{ $student->full_name }}</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.students.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Student Directory
                </a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $student->admission_no }}</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $student->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                    Status: {{ ucfirst($student->status) }}
                </span>
                <a
                    href="{{ route('admin.classes.show', [$student->schoolClass, $student->section]) }}"
                    class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 transition-colors"
                >
                    View Class Roster ({{ $student->schoolClass->name }} - {{ $student->section->name }})
                </a>
            </div>
        </div>

        <!-- 360° Hero Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-900/10 via-transparent to-amber-900/10 dark:from-emerald-950/40 dark:to-slate-800">
                <div class="flex items-center gap-5">
                    <div class="h-20 w-20 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-extrabold font-heading text-2xl shadow-lg shadow-emerald-700/20 ring-4 ring-emerald-500/20">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white">
                                {{ $student->full_name }}
                            </h1>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                {{ $student->admission_no }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-3">
                            <span><strong>Class:</strong> {{ $student->schoolClass->name }} - Section {{ $student->section->name }}</span>
                            <span>•</span>
                            <span><strong>Roll No:</strong> {{ $student->roll_number ?: 'N/A' }}</span>
                            <span>•</span>
                            <span><strong>Admitted:</strong> {{ \Carbon\Carbon::parse($student->admission_date)->format('d M Y') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Quick Stats Badges -->
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 text-center min-w-[110px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Attendance</span>
                        <span class="text-xl font-extrabold font-heading {{ $attendancePct >= 90 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600' }}">
                            {{ $attendancePct }}%
                        </span>
                        <span class="text-[10px] text-slate-500 block">{{ $presentDays }} / {{ $totalDays ?: 1 }} Days</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 text-center min-w-[110px]">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Blood Group</span>
                        <span class="text-xl font-extrabold font-heading text-rose-600 dark:text-rose-400">
                            {{ $student->blood_group ?: 'B+' }}
                        </span>
                        <span class="text-[10px] text-slate-500 block">Medical</span>
                    </div>
                </div>
            </div>

            <!-- Tabbed Grid Panels -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 md:p-8">

                <!-- Left Column: Biodata & Parents -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Personal Information -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Student Demographics
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Date of Birth</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Gender</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 capitalize">{{ $student->gender }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Medium of Study</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">English Medium</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Previous School</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->previous_school ?: 'N/A (Direct Entry)' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Emergency Contact</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->emergency_contact ?: $student->parent_phone }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Curriculum Board</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">Tamil Nadu State Board</span>
                            </div>
                        </div>
                    </div>

                    <!-- Parent / Guardian Information -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Parent / Guardian Details
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Father / Guardian</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->father_name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Mother</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->mother_name ?: 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Contact Phone</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $student->parent_phone }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Parent Email (Login)</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->parent?->email ?: ($student->parent_email ?: 'N/A') }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-400 block text-[11px]">Permanent Address</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->address }}, {{ $student->city }} - {{ $student->pincode }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Payment & Receipts History -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Fee Receipts History
                            </h3>
                            <a
                                href="{{ route('accounts.dashboard') }}#collection"
                                class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                            >
                                Collect Fee
                            </a>
                        </div>

                        @if($student->feeReceipts->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 uppercase text-[10px] font-bold">
                                        <tr>
                                            <th class="px-3 py-2">Receipt No</th>
                                            <th class="px-3 py-2">Date</th>
                                            <th class="px-3 py-2">Amount</th>
                                            <th class="px-3 py-2">Mode</th>
                                            <th class="px-3 py-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @foreach($student->feeReceipts as $rec)
                                            <tr>
                                                <td class="px-3 py-2.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                    {{ $rec->receipt_number }}
                                                </td>
                                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                                    {{ \Carbon\Carbon::parse($rec->payment_date)->format('d M Y') }}
                                                </td>
                                                <td class="px-3 py-2.5 font-bold text-slate-800 dark:text-slate-200">
                                                    ₹{{ number_format($rec->amount_paid, 2) }}
                                                </td>
                                                <td class="px-3 py-2.5 uppercase font-semibold text-slate-500">
                                                    {{ $rec->payment_method }}
                                                </td>
                                                <td class="px-3 py-2.5">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                        PAID
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No fee receipts generated for this academic term yet.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Verification & Promotion Log -->
                <div class="space-y-6">

                    <!-- Document Verification Status -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verified Documents
                        </h3>

                        <div class="space-y-3">
                            @foreach($documents as $doc)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5">
                                        @if($doc['verified'] ?? false)
                                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $doc['name'] }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ ($doc['verified'] ?? false) ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300' }}">
                                        {{ ($doc['verified'] ?? false) ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Promotion & Academic History -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            Academic Progression
                        </h3>

                        @if($student->promotions->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($student->promotions as $promo)
                                    <div class="p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                                        <div class="flex items-center justify-between font-bold text-slate-800 dark:text-slate-200">
                                            <span>{{ $promo->fromClass->name }} → {{ $promo->toClass->name }}</span>
                                            <span class="text-emerald-600 font-mono text-[11px]">Promoted</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">
                                            Academic Session: {{ $promo->academicYear?->name ?? '2025-2026' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center text-xs text-slate-500">
                                Current Admission Class: <strong class="text-slate-800 dark:text-slate-200">{{ $student->schoolClass->name }}</strong>
                                <p class="text-[10px] text-slate-400 mt-1">No prior class transfers on file.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Print Actions -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-2">
                        <button
                            type="button"
                            onclick="window.print()"
                            class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print Student Dossier
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
