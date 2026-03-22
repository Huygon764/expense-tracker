@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest rounded-2xl shadow-editorial-sm p-6 ' . $class]) }}>
    {{ $slot }}
</div>
