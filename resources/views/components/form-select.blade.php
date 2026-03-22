@props(['name', 'label' => '', 'required' => false, 'disabled' => false])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ $label }}</label>
    @endif
    <select name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'block w-full rounded-xl bg-surface-container-low text-on-surface text-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-colors appearance-none' . ($disabled ? ' opacity-60 cursor-not-allowed' : '') . ($errors->has($name) ? ' ring-2 ring-error/30' : '')]) }}>
        {{ $slot }}
    </select>
    @error($name)
        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
    @enderror
</div>
