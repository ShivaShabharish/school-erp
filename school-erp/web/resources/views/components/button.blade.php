@props([
    'variant' => 'primary', // 'primary', 'accent', 'secondary', 'danger', 'outline'
    'size' => 'md', // 'sm', 'md', 'lg'
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer gap-2';

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
];

$variants = [
    'primary' => 'bg-emerald-700 hover:bg-emerald-800 text-white focus:ring-emerald-600 shadow-xs active:bg-emerald-900',
    'accent' => 'bg-amber-600 hover:bg-amber-700 text-white focus:ring-amber-500 shadow-xs active:bg-amber-800',
    'secondary' => 'bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 focus:ring-slate-400',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-xs active:bg-rose-800',
    'outline' => 'border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500',
];

$classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
