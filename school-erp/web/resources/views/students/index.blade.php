<x-layouts.app>
    <x-slot:title>Students & Admissions</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div class="space-y-6" x-data="{ importModal: false }">
        <!-- Top Operations Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Student Enrollment & Directory
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Manage admissions, 360° student records, parent details, and end-of-year batch promotions.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.admissions.enquiries') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Enquiries</span>
                </a>

                <a
                    href="{{ route('admin.students.promotion') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Promotion Wizard</span>
                </a>

                <button
                    type="button"
                    @click="importModal = true"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import CSV</span>
                </button>

                <a
                    href="{{ route('admin.students.export-csv') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer border border-slate-300 dark:border-slate-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export</span>
                </a>

                <a
                    href="{{ route('admin.students.create') }}"
                    class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>New Admission</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card
                title="Total Enrolled"
                :value="$totalActive"
                subtitle="Active in Current Academic Session"
            />
            <x-stat-card
                title="Boys Turnout"
                :value="$totalBoys"
                subtitle="Sanctioned Boys Cohort"
            />
            <x-stat-card
                title="Girls Turnout"
                :value="$totalGirls"
                subtitle="Sanctioned Girls Cohort"
            />
            <x-stat-card
                title="Total Classes"
                :value="count($classes)"
                subtitle="LKG to 12th Standard"
                accent="amber"
            />
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by student name, admission no, or roll no..."
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                </div>

                <!-- Class Filter -->
                <div>
                    <select
                        name="class_id"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                    >
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer"
                    >
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Students</option>
                        <option value="alumni" {{ request('status') === 'alumni' ? 'selected' : '' }}>Alumni</option>
                        <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                </div>

                <!-- Submit & Reset -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 py-2 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold cursor-pointer"
                    >
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'class_id', 'status']))
                        <a
                            href="{{ route('admin.students.index') }}"
                            class="py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Student Records Data Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3.5">Admission No</th>
                            <th class="px-5 py-3.5">Student Name</th>
                            <th class="px-5 py-3.5">Class & Section</th>
                            <th class="px-5 py-3.5">Roll No</th>
                            <th class="px-5 py-3.5">Parent / Contact</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($students as $s)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ $s->admission_no }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-extrabold flex items-center justify-center text-[10px]">
                                            {{ substr($s->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span>{{ $s->full_name }}</span>
                                            <span class="block text-[10px] text-slate-400 font-normal capitalize">{{ $s->gender }} &bull; {{ $s->blood_group ?? 'O+' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $s->schoolClass?->name }} - Section {{ $s->section?->name }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600 dark:text-slate-400">
                                    {{ $s->roll_number ?? '--' }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">
                                    <div>
                                        <span class="font-medium">{{ $s->father_name ?? 'Parent' }}</span>
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $s->parent_phone ?? $s->emergency_contact }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-badge variant="{{ $s->status === 'active' ? 'success' : 'neutral' }}">
                                        {{ ucfirst($s->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a
                                        href="{{ route('admin.students.show', $s) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs transition-colors"
                                    >
                                        <span>360° Profile</span>
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <x-empty-state
                                        title="No Students Found"
                                        description="No student profiles match your search criteria or filter selection."
                                        actionText="Clear Filters"
                                        :actionRoute="route('admin.students.index')"
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        <!-- CSV Bulk Import Modal -->
        <div
            x-show="importModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs"
        >
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700" @click.outside="importModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-base font-bold font-heading text-slate-900 dark:text-white">Bulk Student CSV Import</h3>
                    <button @click="importModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.students.import-csv') }}" enctype="multipart/form-data" class="space-y-4 py-4 text-xs">
                    @csrf
                    <div class="p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 text-center space-y-2 bg-slate-50 dark:bg-slate-900/40">
                        <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="font-semibold text-slate-700 dark:text-slate-300">Choose CSV File to Upload</p>
                        <input
                            type="file"
                            name="csv_file"
                            accept=".csv,text/csv"
                            required
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                        />
                    </div>

                    <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                        <div>
                            <p class="font-bold">Need the standard format?</p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">Download the pre-formatted CSV template.</p>
                        </div>
                        <a
                            href="{{ route('admin.students.sample-csv') }}"
                            class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs"
                        >
                            Download Sample
                        </a>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="importModal = false" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">
                            Upload & Enroll Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
