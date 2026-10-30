<x-layouts.app>
    <x-slot:title>Prospective Admission Enquiries</x-slot:title>
    <x-slot:headerTitle>Students & Admissions</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newEnquiryModal: false,
            convertModal: false,
            selectedEnquiry: null,
            selectedEnquiryClass: null,
            sectionsForClass: [],
            classes: {{ Js::from($classes) }},
            openConvert(enquiry) {
                this.selectedEnquiry = enquiry;
                const found = this.classes.find(c => c.id == enquiry.school_class_id);
                this.sectionsForClass = found ? found.sections : [];
                this.convertModal = true;
            }
        }"
    >
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
                    <span class="text-slate-800 dark:text-slate-200">Enquiries Pipeline</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Prospective Admission Pipeline
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Track incoming student applications, schedule parent interviews, and convert eligible applicants directly into the roster.
                </p>
            </div>

            <!-- Action Button -->
            <button
                type="button"
                @click="newEnquiryModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Register Prospective Enquiry</span>
            </button>
        </div>

        <!-- Filter Tabs & Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a
                href="{{ route('admin.admissions.enquiries') }}"
                class="p-4 rounded-xl border {{ !request('status') || request('status') === 'all' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Pipeline</span>
                <span class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1 block">
                    {{ $counts['total'] }}
                </span>
                <span class="text-[10px] text-slate-500">All applications</span>
            </a>

            <a
                href="{{ route('admin.admissions.enquiries', ['status' => 'pending']) }}"
                class="p-4 rounded-xl border {{ request('status') === 'pending' ? 'border-amber-600 bg-amber-50/50 dark:bg-amber-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-500 block">Pending Review</span>
                <span class="text-2xl font-extrabold font-heading text-amber-600 dark:text-amber-400 mt-1 block">
                    {{ $counts['pending'] }}
                </span>
                <span class="text-[10px] text-slate-500">Awaiting interview</span>
            </a>

            <a
                href="{{ route('admin.admissions.enquiries', ['status' => 'reviewed']) }}"
                class="p-4 rounded-xl border {{ request('status') === 'reviewed' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-500 block">Reviewed</span>
                <span class="text-2xl font-extrabold font-heading text-blue-600 dark:text-blue-400 mt-1 block">
                    {{ $counts['reviewed'] }}
                </span>
                <span class="text-[10px] text-slate-500">Interview cleared</span>
            </a>

            <a
                href="{{ route('admin.admissions.enquiries', ['status' => 'admitted']) }}"
                class="p-4 rounded-xl border {{ request('status') === 'admitted' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-colors"
            >
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-500 block">Converted</span>
                <span class="text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $counts['admitted'] }}
                </span>
                <span class="text-[10px] text-slate-500">Enrolled into roster</span>
            </a>
        </div>

        <!-- Search & Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
            <!-- Search Bar -->
            <form action="{{ route('admin.admissions.enquiries') }}" method="GET" class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by Enquiry No, Student Name, Parent Name, or Phone..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                    <svg class="h-4 w-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all cursor-pointer"
                >
                    Filter
                </button>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-5 py-3">Enquiry No</th>
                            <th class="px-5 py-3">Student Name</th>
                            <th class="px-5 py-3">Class Desired</th>
                            <th class="px-5 py-3">Parent Details</th>
                            <th class="px-5 py-3">Contact Phone</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @forelse($enquiries as $enq)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $enq->enquiry_no }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    {{ $enq->full_name }}
                                    <span class="block text-[10px] text-slate-400 font-normal capitalize">{{ $enq->gender }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-semibold">
                                    {{ $enq->schoolClass->name }}
                                </td>
                                <td class="px-5 py-3.5">
                                    {{ $enq->parent_name }}
                                </td>
                                <td class="px-5 py-3.5 font-mono font-semibold">
                                    {{ $enq->phone }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($enq->status === 'admitted')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                            ADMITTED
                                        </span>
                                    @elseif($enq->status === 'reviewed')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-300 dark:border-blue-800">
                                            REVIEWED
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                            PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if($enq->status !== 'admitted')
                                        <button
                                            type="button"
                                            @click="openConvert({{ Js::from($enq) }})"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-[11px] font-bold transition-all shadow-xs cursor-pointer"
                                        >
                                            Convert to Admission
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Enrolled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    No prospective enquiries found matching the filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($enquiries->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $enquiries->links() }}
                </div>
            @endif
        </div>

        <!-- Modal: Register New Enquiry -->
        <div
            x-show="newEnquiryModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="newEnquiryModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Register Prospective Enquiry
                    </h3>
                    <button type="button" @click="newEnquiryModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.admissions.enquiry.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Student First Name *</label>
                            <input type="text" name="first_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Student Last Name</label>
                            <input type="text" name="last_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Gender *</label>
                            <select name="gender" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Class Desired *</label>
                            <select name="school_class_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Parent / Guardian *</label>
                            <input type="text" name="parent_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Contact Phone *</label>
                            <input type="tel" name="phone" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Parent Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Notes / Remarks</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="newEnquiryModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Submit Enquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Convert Enquiry to Admission -->
        <div
            x-show="convertModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="convertModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Convert to Student Admission
                    </h3>
                    <button type="button" @click="convertModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedEnquiry">
                    <form :action="`/admin/admissions/enquiry/${selectedEnquiry.id}/convert`" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 text-xs space-y-1">
                            <div><strong>Candidate:</strong> <span x-text="selectedEnquiry.first_name + ' ' + (selectedEnquiry.last_name || '')"></span></div>
                            <div><strong>Enquiry No:</strong> <span class="font-mono text-emerald-600 dark:text-emerald-400" x-text="selectedEnquiry.enquiry_no"></span></div>
                            <div><strong>Parent:</strong> <span x-text="selectedEnquiry.parent_name + ' (' + selectedEnquiry.phone + ')'"></span></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Select Section for Placement *
                            </label>
                            <select
                                name="section_id"
                                required
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <template x-for="s in sectionsForClass" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" @click="convertModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Confirm Admission
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-layouts.app>
