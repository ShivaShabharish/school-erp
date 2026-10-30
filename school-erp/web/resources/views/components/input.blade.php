@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => '',
    'error' => null,
    'help' => null,
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg border text-sm transition-colors px-3 py-2 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 ' .
                ($errors->has($name) || $error
                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200 dark:border-rose-500'
                    : 'border-slate-300 dark:border-slate-600 focus:border-emerald-600 focus:ring-emerald-100 dark:focus:ring-emerald-950')
            ]) }}
        />
    </div>

    @if($help && !$errors->has($name) && !$error)
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @else
        @if($error)
            <p class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ $error }}</p>
        @endif
    @enderror
</div>
