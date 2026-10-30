<x-layouts.app>
    <x-slot:title>Parent-Teacher Communications & Notes</x-slot:title>
    <x-slot:headerTitle>Communication &bull; Parent Note Inbox</x-slot:headerTitle>

    <div
        class="space-y-6"
        x-data="{
            newNoteModal: false,
            replyModal: false,
            activeNote: null,
            openReply(note) {
                this.activeNote = note;
                this.replyModal = true;
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
                        Dashboard
                    </a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200">Parent Communications</span>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white tracking-tight">
                    Parent Inquiries & Teacher Notes Inbox
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Direct two-way correspondence between parents, class teachers, and school principal.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                    {{ $pendingCount }} Pending Responses
                </span>

                @if(auth()->user()->isParent())
                    <button
                        type="button"
                        @click="newNoteModal = true"
                        class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Write to Teacher</span>
                    </button>
                @endif
            </div>
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
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <a
                href="{{ route('admin.communication.notes.index') }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $status === 'all' ? 'bg-emerald-700 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                All Messages
            </a>
            <a
                href="{{ route('admin.communication.notes.index', ['status' => 'pending']) }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                Pending ({{ $pendingCount }})
            </a>
            <a
                href="{{ route('admin.communication.notes.index', ['status' => 'replied']) }}"
                class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ $status === 'replied' ? 'bg-emerald-700 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}"
            >
                Replied
            </a>
        </div>

        <!-- Notes Threads List -->
        <div class="space-y-4">
            @forelse($notes as $note)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $note->status === 'replied' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                {{ $note->status }}
                            </span>
                            <h3 class="font-bold font-heading text-sm text-slate-900 dark:text-white">
                                {{ $note->subject }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-slate-400 font-mono">
                            <span>{{ $note->created_at->format('d M Y, h:i A') }}</span>
                            @if(!auth()->user()->isParent() && $note->status === 'pending')
                                <button
                                    type="button"
                                    @click="openReply({{ Js::from($note) }})"
                                    class="px-3 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs cursor-pointer font-sans"
                                >
                                    Reply
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Parent's Inquiry -->
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs shrink-0">
                            P
                        </div>
                        <div class="flex-1 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900 dark:text-white">
                                    {{ $note->parent?->name }} (Parent of {{ $note->student?->full_name }})
                                </span>
                                <span class="text-[10px] text-slate-400">Class {{ $note->student?->schoolClass?->name }}-{{ $note->student?->section?->name }}</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $note->message }}
                            </p>
                        </div>
                    </div>

                    <!-- School Response (if replied) -->
                    @if($note->reply)
                        <div class="flex items-start gap-3 pl-6">
                            <div class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs shrink-0">
                                S
                            </div>
                            <div class="flex-1 bg-emerald-50/60 dark:bg-emerald-950/20 p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-800/40 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-emerald-900 dark:text-emerald-300">
                                        Faculty / Principal Response
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $note->replied_at?->format('d M Y, h:i A') }}</span>
                                </div>
                                <p class="text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                                    {{ $note->reply }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center text-slate-400 text-xs">
                    No parent notes in this folder.
                </div>
            @endforelse
        </div>

        @if($notes->hasPages())
            <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                {{ $notes->links() }}
            </div>
        @endif

        <!-- Reply Modal -->
        <div
            x-show="replyModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
            x-cloak
        >
            <div
                @click.away="replyModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-heading font-extrabold text-lg text-slate-900 dark:text-white">
                        Reply to Parent Note
                    </h3>
                    <button @click="replyModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form
                    method="POST"
                    :action="activeNote ? '/admin/communication/notes/' + activeNote.id + '/reply' : '#'"
                    class="space-y-4 mt-4"
                >
                    @csrf
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Subject</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-sm block" x-text="activeNote?.subject"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Your Response *</label>
                        <textarea
                            name="reply"
                            rows="5"
                            required
                            placeholder="Write official response to parent..."
                            class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 p-2.5 text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                        <button
                            type="button"
                            @click="replyModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer"
                        >
                            Dispatch Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
