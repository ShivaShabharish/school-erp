@props([
    'title' => 'No records found',
    'description' => 'There is no data to display right now.',
    'actionText' => null,
    'actionRoute' => null,
])

<div class="flex flex-col items-center justify-center p-12 text-center rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/50 dark:bg-slate-800/50">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 mb-4">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    </div>
    <h3 class="text-base font-bold font-heading text-slate-800 dark:text-slate-100">{{ $title }}</h3>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm">{{ $description }}</p>
    @if($actionText && $actionRoute)
        <div class="mt-5">
            <a href="{{ $actionRoute }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-xs">
                {{ $actionText }}
            </a>
        </div>
    @endif
</div>
