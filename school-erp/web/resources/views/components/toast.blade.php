<div
    x-data="{
        messages: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.messages.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    x-on:toast.window="add($event.detail.message, $event.detail.type || 'success')"
    x-init="
        @if(session('success')) add('{{ addslashes(session('success')) }}', 'success'); @endif
        @if(session('error')) add('{{ addslashes(session('error')) }}', 'danger'); @endif
        @if(session('info')) add('{{ addslashes(session('info')) }}', 'info'); @endif
        @if(session('warning')) add('{{ addslashes(session('warning')) }}', 'warning'); @endif
    "
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
>
    <template x-for="item in messages" :key="item.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="pointer-events-auto flex items-center justify-between p-4 rounded-xl shadow-lg border text-sm"
            :class="{
                'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border-emerald-500/30 border-l-4 border-l-emerald-600': item.type === 'success',
                'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border-rose-500/30 border-l-4 border-l-rose-600': item.type === 'danger',
                'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border-amber-500/30 border-l-4 border-l-amber-600': item.type === 'warning',
                'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border-sky-500/30 border-l-4 border-l-sky-600': item.type === 'info'
            }"
        >
            <div class="flex items-center gap-3">
                <template x-if="item.type === 'success'">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template x-if="item.type === 'danger'">
                    <svg class="h-5 w-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <span x-text="item.message" class="font-medium text-xs"></span>
            </div>
            <button @click="remove(item.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-3">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
