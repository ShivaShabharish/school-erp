<x-layouts.app>
    <x-slot:title>Official Certificates Registry (TC & Bonafide)</x-slot:title>
    <x-slot:headerTitle>Records &bull; Certificates Registry</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newCertModal: false,
            classes: {{ Js::from($classes) }},
            certType: 'bonafide',
            selectedClass: '',
            get classStudents() {
                const c = this.classes.find(item => item.id == this.selectedClass);
                return c ? c.students : [];
            }
        }"
    >
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
                    <span class="text-slate-800 dark:text-slate-200">Certificates Master</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Official Student Certificates Registry
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Issue and download official Tamil Nadu State Board Transfer Certificates (TC) and Bonafide Certificates with unique sequential identifiers.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.communication.id-cards.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2"
                >
                    <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <span>ID Cards Print</span>
                </a>

                <button
                    type="button"
                    @click="newCertModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Issue New Certificate</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @if(session('cert_id'))
                    <a
                        href="{{ route('admin.communication.certificates.download', session('cert_id')) }}"
                        class="px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download PDF</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- Summary Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Bonafide Certificates</span>
                <div class="mt-2 text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                    {{ $bonafideCount }} Issued
                </div>
                <p class="mt-1 text-xs text-slate-500">For passports, bank accounts, competitions</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Transfer Certificates (TC)</span>
                <div class="mt-2 text-2xl font-bold font-heading text-amber-600 dark:text-amber-400">
                    {{ $tcCount }} Issued
                </div>
                <p class="mt-1 text-xs text-slate-500">Official TN State Board Leaving Certificates</p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">Academic Year Cycle</span>
                <div class="mt-2 text-2xl font-bold font-heading text-slate-900 dark:text-white">
                    {{ $currentYear?->name ?? '2026-2027' }}
                </div>
                <p class="mt-1 text-xs text-slate-500">Tamil Nadu State Board Registered</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <a
                href="{{ route('admin.communication.certificates.index') }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $type === 'all' ? 'bg-emerald-700 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                All Certificates
            </a>
            <a
                href="{{ route('admin.communication.certificates.index', ['type' => 'bonafide']) }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $type === 'bonafide' ? 'bg-emerald-700 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                Bonafide ({{ $bonafideCount }})
            </a>
            <a
                href="{{ route('admin.communication.certificates.index', ['type' => 'transfer_certificate']) }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $type === 'transfer_certificate' ? 'bg-emerald-700 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                Transfer Certificates ({{ $tcCount }})
            </a>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">Certificate No</th>
                            <th class="px-4 py-3">Student Name</th>
                            <th class="px-4 py-3">Admission No</th>
                            <th class="px-4 py-3">Class & Sec</th>
                            <th class="px-4 py-3">Certificate Type</th>
                            <th class="px-4 py-3">Date of Issue</th>
                            <th class="px-4 py-3">Purpose / Reason</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                        @forelse($certificates as $cert)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3.5 font-mono font-bold text-amber-700 dark:text-amber-400 whitespace-nowrap">
                                    {{ $cert->certificate_no }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $cert->student?->full_name }}
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $cert->student?->admission_no }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    Class {{ $cert->student?->schoolClass?->name }} - {{ $cert->student?->section?->name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cert->certificate_type === 'bonafide' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                        {{ str_replace('_', ' ', $cert->certificate_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600 dark:text-slate-300">
                                    {{ $cert->issue_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 max-w-xs truncate">
                                    {{ $cert->reason }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a
                                        href="{{ route('admin.communication.certificates.download', $cert) }}"
                                        class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold inline-flex items-center gap-1"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                    No certificates issued matching this criteria. Click "Issue New Certificate" above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($certificates->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $certificates->links() }}
                </div>
            @endif
        </div>

        <!-- Issue Certificate Modal -->
        <div
            x-show="newCertModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newCertModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Issue Official Certificate
                    </h3>
                    <button @click="newCertModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.communication.certificates.generate') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Certificate Type *</label>
                        <select
                            name="certificate_type"
                            x-model="certType"
                            required
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                        >
                            <option value="bonafide">Bonafide Certificate (Study Certificate)</option>
                            <option value="transfer_certificate">Transfer Certificate (TC - School Leaving)</option>
                            <option value="conduct">Conduct & Character Certificate</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Class</label>
                            <select
                                x-model="selectedClass"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="">-- Choose Class --</option>
                                <template x-for="cl in classes" :key="cl.id">
                                    <option :value="cl.id" x-text="cl.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Student *</label>
                            <select
                                name="student_id"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="">-- Select Student --</option>
                                <template x-for="st in classStudents" :key="st.id">
                                    <option :value="st.id" x-text="st.first_name + ' ' + (st.last_name || '') + ' (' + st.admission_no + ')'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Date of Issue *</label>
                        <input
                            type="date"
                            name="issue_date"
                            required
                            value="{{ now()->toDateString() }}"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Purpose / Reason *</label>
                        <input
                            type="text"
                            name="reason"
                            required
                            placeholder="e.g. Higher Education Admission / Passport Application"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <!-- Transfer Certificate specific fields -->
                    <div x-show="certType === 'transfer_certificate'" class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-amber-600 uppercase block">Transfer Certificate Particulars</span>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Date of Leaving School</label>
                                <input
                                    type="date"
                                    name="leaving_date"
                                    value="{{ now()->toDateString() }}"
                                    class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white font-mono"
                                />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">General Conduct</label>
                                <select
                                    name="conduct"
                                    class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                                >
                                    <option value="Good">Good</option>
                                    <option value="Very Good">Very Good</option>
                                    <option value="Exemplary">Exemplary</option>
                                    <option value="Satisfactory">Satisfactory</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="fee_dues_cleared" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span>All school fee dues, library books & sports kit cleared</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newCertModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Generate Certificate & PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
