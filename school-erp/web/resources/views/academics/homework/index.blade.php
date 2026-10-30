<x-layouts.app>
    <x-slot:title>Homework & Class Assignments</x-slot:title>
    <x-slot:headerTitle>Academics & Timetables</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newHwModal: false,
            classes: {{ Js::from($classes) }},
            selectedClass: '{{ $selectedClassId }}',
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
                    <a href="{{ route('admin.academics.timetables.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Weekly Timetables
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Homework Board</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Homework & Curriculum Assignments
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Assign and monitor daily classroom assignments, readings, and homework tasks across sections.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="newHwModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Assign Homework Task</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
            <form method="GET" action="{{ route('admin.academics.homework.index') }}" class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Filter Standard:</label>
                    <select
                        name="class_id"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">All Standards</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="px-4 py-1.5 rounded-lg bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white font-bold transition-colors cursor-pointer"
                >
                    Filter Tasks
                </button>
            </form>
        </div>

        <!-- Homework List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($assignments as $hw)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-xs space-y-3 hover:border-emerald-500/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 uppercase">
                            {{ $hw->schoolClass?->name }} - Section {{ $hw->section?->name }}
                        </span>
                        <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">
                            Due: {{ \Carbon\Carbon::parse($hw->due_date)->format('D, d M Y') }}
                        </span>
                    </div>

                    <div>
                        <div class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">{{ $hw->subject?->name }}</div>
                        <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white mt-0.5">
                            {{ $hw->title }}
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-3">
                            {{ $hw->description }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Assigned by: <strong class="text-slate-700 dark:text-slate-300">{{ $hw->teacher?->user?->name }}</strong></span>
                        <span>Date: {{ \Carbon\Carbon::parse($hw->assigned_date)->format('d M') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center text-slate-400 text-xs">
                    No active homework tasks assigned for this class. Click "Assign Homework Task" above.
                </div>
            @endforelse
        </div>

        @if($assignments->hasPages())
            <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
                {{ $assignments->links() }}
            </div>
        @endif

        <!-- Modal: Assign Homework -->
        <div
            x-show="newHwModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-cloak
        >
            <div
                @click.away="newHwModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="text-base font-extrabold font-heading text-slate-900 dark:text-white">
                        Create Homework Assignment
                    </h3>
                    <button type="button" @click="newHwModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.academics.homework.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Standard / Class *</label>
                            <select name="school_class_id" x-model="selectedClass" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Section *</label>
                            <select name="section_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <template x-for="s in currentSections" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Subject *</label>
                            <select name="subject_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teacher *</label>
                            <select name="staff_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->user?->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Assignment Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Science Chapter 4 Problem Set" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description & Questions *</label>
                        <textarea name="description" rows="3" required placeholder="Specific exercises, page numbers, or research prompts..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Assigned Date *</label>
                            <input type="date" name="assigned_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Due Date *</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+2 days')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="newHwModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Assign Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
