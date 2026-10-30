<x-layouts.app>
    <x-slot:title>School Circulars & Notice Board</x-slot:title>
    <x-slot:headerTitle>Communication &bull; Notice Board</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newNoticeModal: false,
            classes: {{ Js::from($classes) }},
            targetType: 'all',
            selectedClass: '',
            get currentSections() {
                const c = this.classes.find(item => item.id == this.selectedClass);
                return c ? c.sections : [];
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
                    <a href="{{ route('admin.communication.calendar.index') }}" class="hover:text-emerald-600">Calendar</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Announcements</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Official Circulars & Notice Board
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Targeted announcements dispatched to specific classes, teachers, parents, or broadcast campus-wide.
                </p>
            </div>

            <button
                type="button"
                @click="newNoticeModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer self-start md:self-auto"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Publish Circular</span>
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto">
                <a
                    href="{{ route('admin.communication.announcements.index') }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $target === 'all' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    All Notices
                </a>
                <a
                    href="{{ route('admin.communication.announcements.index', ['target' => 'class']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $target === 'class' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    Class-Specific
                </a>
                <a
                    href="{{ route('admin.communication.announcements.index', ['target' => 'staff']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $target === 'staff' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    Staff Only
                </a>
                <a
                    href="{{ route('admin.communication.announcements.index', ['target' => 'parents']) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors {{ $target === 'parents' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}"
                >
                    Parents Only
                </a>
            </div>

            <form method="GET" action="{{ route('admin.communication.announcements.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search notice titles..."
                    class="text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                />
                <button
                    type="submit"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold"
                >
                    Filter
                </button>
            </form>
        </div>

        <!-- Announcements Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($announcements as $ann)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-xs flex flex-col justify-between {{ $ann->is_pinned ? 'ring-2 ring-amber-500/30 border-amber-500/40' : '' }}">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ann->category === 'urgent' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : ($ann->category === 'academic' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                {{ $ann->category }}
                            </span>

                            @if($ann->is_pinned)
                                <span class="flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                    </svg>
                                    PINNED
                                </span>
                            @endif
                        </div>

                        <h3 class="font-bold font-heading text-slate-900 dark:text-white text-base line-clamp-2">
                            {{ $ann->title }}
                        </h3>

                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-4 leading-relaxed">
                            {{ $ann->content }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-400">
                        <div class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $ann->publish_date->format('d M Y') }}</span>
                        </div>

                        <span class="font-semibold text-slate-500">
                            @if($ann->target_type === 'class')
                                {{ $ann->schoolClass?->name }} {{ $ann->section ? '(' . $ann->section->name . ')' : '' }}
                            @else
                                {{ ucfirst($ann->target_type) }}
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center text-slate-400 text-xs">
                    No circulars match the selected filter. Click "Publish Circular" above.
                </div>
            @endforelse
        </div>

        @if($announcements->hasPages())
            <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                {{ $announcements->links() }}
            </div>
        @endif

        <!-- Publish Notice Modal -->
        <div
            x-show="newNoticeModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="newNoticeModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Publish Official Notice / Circular
                    </h3>
                    <button @click="newNoticeModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.communication.announcements.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $currentYear?->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notice Headline *</label>
                        <input
                            type="text"
                            name="title"
                            required
                            placeholder="e.g. Mandatory Parent-Teacher Meeting, Winter Uniform Guidelines"
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category *</label>
                            <select
                                name="category"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                            >
                                <option value="general">General Notice</option>
                                <option value="academic">Academic & Exams</option>
                                <option value="urgent">Urgent / Important</option>
                                <option value="event">School Function / Event</option>
                                <option value="sports">Sports & Physical Ed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Target Audience *</label>
                            <select
                                name="target_type"
                                x-model="targetType"
                                required
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-semibold"
                            >
                                <option value="all">Entire School Broadcast</option>
                                <option value="class">Specific Class</option>
                                <option value="staff">Staff Members Only</option>
                                <option value="parents">Parents Only</option>
                            </select>
                        </div>
                    </div>

                    <!-- Conditional Class & Section Selectors -->
                    <div class="grid grid-cols-2 gap-3" x-show="targetType === 'class'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Class *</label>
                            <select
                                name="school_class_id"
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
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Section (Optional)</label>
                            <select
                                name="section_id"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                            >
                                <option value="">All Sections</option>
                                <template x-for="sec in currentSections" :key="sec.id">
                                    <option :value="sec.id" x-text="sec.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Detailed Content *</label>
                        <textarea
                            name="content"
                            rows="4"
                            required
                            placeholder="Write circular description and instructions..."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2 text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Publish Date *</label>
                            <input
                                type="date"
                                name="publish_date"
                                required
                                value="{{ now()->toDateString() }}"
                                class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white font-mono"
                            />
                        </div>

                        <div class="flex items-center pt-5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="is_pinned" value="1" class="rounded text-amber-600 focus:ring-amber-500">
                                <span>Pin to Top of Notice Board</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="newNoticeModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Publish Notice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
