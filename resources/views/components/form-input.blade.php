@props(['name', 'label' => '', 'type' => 'text', 'value' => '', 'required' => false, 'disabled' => false, 'placeholder' => '', 'icon' => null, 'readonly' => false])

@php
    $val = old($name, $value);
@endphp

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ $label }}</label>
    @endif
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant">
                <x-icon :name="$icon" class="w-4 h-4" />
            </div>
        @endif
        <input type="{{ $type }}"
               name="{{ $name }}"
               id="{{ $name }}"
               value="{{ $val }}"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               {{ $readonly ? 'readonly' : '' }}
               {{ $attributes->merge(['class' => 'block w-full rounded-xl bg-surface-container-low text-on-surface text-sm py-3 ' . ($icon ? 'pl-10 pr-4' : 'px-4') . ' placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-colors' . ($disabled || $readonly ? ' opacity-60 cursor-not-allowed' : '') . ($errors->has($name) ? ' ring-2 ring-error/30' : '')]) }}
        >
    </div>
    @error($name)
        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
    @enderror
</div>
