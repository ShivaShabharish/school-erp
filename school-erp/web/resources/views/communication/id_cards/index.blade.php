<x-layouts.app>
    <x-slot:title>Identity Cards & Batch Printing</x-slot:title>
    <x-slot:headerTitle>Records &bull; ID Cards</x-slot:headerTitle>

    <div class="space-y-6">
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
                    <a href="{{ route('admin.communication.certificates.index') }}" class="hover:text-emerald-600">Certificates</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">ID Cards</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Student & Staff Identity Cards
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Batch generation, barcode rendering, and CR-80 card printing templates for Test Subject HSS.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.communication.id-cards.print-staff') }}"
                    target="_blank"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Staff ID Cards
                </a>

                <a
                    href="{{ route('admin.communication.id-cards.print-students', ['class_id' => $selectedClassId, 'section_id' => $selectedSectionId]) }}"
                    target="_blank"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Class Sheet (A4)
                </a>
            </div>
        </div>

        {{-- Filter Strip --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-700 p-5">
            <form method="GET" action="{{ route('admin.communication.id-cards.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Select Class</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                Class {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @php
                    $currentClass = $classes->firstWhere('id', $selectedClassId);
                    $sections = $currentClass?->sections ?? collect();
                @endphp
                @if($sections->isNotEmpty())
                    <div class="min-w-[150px]">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Select Section</label>
                        <select name="section_id" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>
                                    Section {{ $sec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-end pt-5">
                    <span class="text-xs text-slate-500">
                        Displaying preview cards for <strong>Class {{ $currentClass?->name }}</strong> (Academic Year {{ $currentYear?->name ?? '2026-27' }}).
                    </span>
                </div>
            </form>
        </div>

        {{-- Student Cards Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Student ID Cards Preview
                </h2>
                <a href="{{ route('admin.communication.id-cards.print-students', ['class_id' => $selectedClassId, 'section_id' => $selectedSectionId]) }}" target="_blank"
                   class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline">
                    Open Full Class Print Sheet &rarr;
                </a>
            </div>

            @if($previewStudents->isEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-500">
                    No active students found in this class/section.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($previewStudents as $student)
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md border-2 border-emerald-600 overflow-hidden w-full max-w-[280px] mx-auto flex flex-col justify-between transition hover:shadow-lg">
                            {{-- Card Header --}}
                            <div class="bg-gradient-to-r from-emerald-800 to-emerald-700 text-white text-center py-2.5 px-3">
                                <div class="text-[11px] font-bold tracking-wider uppercase leading-tight">Test Subject HSS</div>
                                <div class="text-[8px] text-emerald-100 uppercase tracking-widest mt-0.5">Pudukkottai, Tamil Nadu</div>
                            </div>

                            {{-- Student Photo & Badge --}}
                            <div class="p-4 text-center">
                                <div class="relative inline-block mb-3">
                                    <div class="w-20 h-20 rounded-full mx-auto border-2 border-emerald-600 bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xl overflow-hidden shadow-sm">
                                        @if($student->avatar_url)
                                            <img src="{{ $student->avatar_url }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="absolute bottom-0 right-1 px-1.5 py-0.2 bg-amber-500 text-white text-[9px] font-bold rounded-full">
                                        {{ $student->blood_group ?: 'O+' }}
                                    </span>
                                </div>

                                <div class="font-bold text-slate-900 dark:text-white text-sm leading-snug">{{ $student->full_name }}</div>
                                <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Class {{ $student->schoolClass?->name }} - {{ $student->section?->name }}</div>

                                <div class="mt-3 text-left text-[11px] space-y-1 bg-slate-50 dark:bg-slate-900 p-2.5 rounded-lg border border-slate-100 dark:border-slate-800">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Adm No:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student->admission_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Roll No:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->roll_number ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">DOB:</span>
                                        <span class="text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Emergency:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->emergency_contact ?: $student->parent_phone ?: '98401 23456' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Footer with Barcode --}}
                            <div class="bg-slate-100 dark:bg-slate-900 px-3 py-2 border-t border-slate-200 dark:border-slate-800 text-center">
                                <div class="font-mono text-[9px] tracking-widest text-slate-600 dark:text-slate-400 mb-0.5 font-bold">
                                    *{{ $student->admission_number }}*
                                </div>
                                <div class="h-3 flex items-center justify-center gap-0.5 opacity-80">
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-2 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Staff Cards Section --}}
        <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Faculty & Staff ID Cards Preview
                </h2>
                <a href="{{ route('admin.communication.id-cards.print-staff') }}" target="_blank"
                   class="text-xs font-bold text-amber-600 hover:text-amber-700 underline">
                    Open Full Staff Print Sheet &rarr;
                </a>
            </div>

            @if($previewStaff->isEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-500">
                    No active staff members found.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($previewStaff as $staff)
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md border-2 border-amber-600 overflow-hidden w-full max-w-[280px] mx-auto flex flex-col justify-between transition hover:shadow-lg">
                            <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white text-center py-2.5 px-3">
                                <div class="text-[11px] font-bold tracking-wider uppercase leading-tight">Test Subject HSS</div>
                                <div class="text-[8px] text-amber-300 uppercase tracking-widest mt-0.5">Faculty & Staff Card</div>
                            </div>

                            <div class="p-4 text-center">
                                <div class="relative inline-block mb-3">
                                    <div class="w-20 h-20 rounded-full mx-auto border-2 border-amber-500 bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xl overflow-hidden shadow-sm">
                                        <span>{{ substr($staff->user->first_name ?? 'S', 0, 1) }}{{ substr($staff->user->last_name ?? 'T', 0, 1) }}</span>
                                    </div>
                                    <span class="absolute bottom-0 right-1 px-1.5 py-0.2 bg-emerald-600 text-white text-[9px] font-bold rounded-full">
                                        {{ $staff->blood_group ?: 'B+' }}
                                    </span>
                                </div>

                                <div class="font-bold text-slate-900 dark:text-white text-sm leading-snug">{{ $staff->user->name ?? $staff->employee_code }}</div>
                                <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $staff->designation ?? 'Teacher' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $staff->department ?? 'Academics' }}</div>

                                <div class="mt-3 text-left text-[11px] space-y-1 bg-slate-50 dark:bg-slate-900 p-2.5 rounded-lg border border-slate-100 dark:border-slate-800">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Emp Code:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $staff->employee_code }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Mobile:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->user->phone ?: '+91 98400 11223' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Valid Till:</span>
                                        <span class="text-slate-800 dark:text-slate-200">31/05/2027</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-100 dark:bg-slate-900 px-3 py-2 border-t border-slate-200 dark:border-slate-800 text-center">
                                <div class="font-mono text-[9px] tracking-widest text-slate-600 dark:text-slate-400 mb-0.5 font-bold">
                                    *{{ $staff->employee_code }}*
                                </div>
                                <div class="h-3 flex items-center justify-center gap-0.5 opacity-80">
                                    <span class="w-1 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-2 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-0.5 h-full bg-slate-800 dark:bg-slate-300"></span>
                                    <span class="w-1 h-full bg-slate-800 dark:bg-slate-300"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
