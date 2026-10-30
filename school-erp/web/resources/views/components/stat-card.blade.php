@props([
    'title',
    'value',
    'subtitle' => null,
    'change' => null,
    'changeType' => 'positive', // 'positive', 'negative', 'neutral'
    'live' => false,
    'icon' => null,
    'accent' => 'emerald', // 'emerald', 'amber', 'slate'
])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 p-5 shadow-xs border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $title }}</span>
        @if($live)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                LIVE
            </span>
        @elseif($icon)
            <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $accent === 'amber' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' }}">
                {!! $icon !!}
            </div>
        @endif
    </div>

    <div class="mt-3 flex items-baseline justify-between">
        <div class="text-2xl font-bold font-heading text-slate-900 dark:text-white">
            {{ $value }}
        </div>
        @if($change)
            <span class="inline-flex items-center text-xs font-medium {{ $changeType === 'positive' ? 'text-emerald-600 dark:text-emerald-400' : ($changeType === 'negative' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500') }}">
                {{ $change }}
            </span>
        @endif
    </div>

    @if($subtitle)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif
</div>
