@props([
    'rows' => 3,
])

<div class="space-y-3 animate-pulse">
    @for($i = 0; $i < $rows; $i++)
        <div class="h-10 bg-slate-200 dark:bg-slate-700/60 rounded-lg w-full"></div>
    @endfor
</div>
