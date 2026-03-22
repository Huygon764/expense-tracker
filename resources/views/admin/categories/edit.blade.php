@extends('layouts.app')

@section('page-title', __('messages.edit_default_category'))

@section('content')
<div class="max-w-lg space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <x-btn :href="route('admin.categories.index')" variant="ghost" size="sm" icon="arrow-left">
            {{ __('messages.back') }}
        </x-btn>
        <h1 class="text-2xl font-display font-bold text-on-surface">{{ __('messages.edit_default_category') }}: {{ $category->name }}</h1>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-form-input
                name="name"
                :label="__('messages.name')"
                :value="$category->name"
                :required="true"
                icon="tag"
            />

            <x-form-input
                name="icon"
                :label="__('messages.icon')"
                :value="$category->icon"
                placeholder="e.g. food emoji"
            />

            <div>
                <label for="color" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ __('messages.color') }}</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', $category->color ?? '#6366f1') }}"
                        class="h-10 w-16 cursor-pointer rounded-xl bg-surface-container-low p-1">
                    <span class="text-xs text-on-surface-variant">{{ __('messages.color_picker_hint') }}</span>
                </div>
                @error('color')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <x-form-input
                name="sort_order"
                :label="__('messages.sort_order')"
                type="number"
                :value="$category->sort_order"
                min="0"
            />

            <div class="flex gap-3 pt-2" style="border-top: 1px solid rgba(191,201,200,0.15);">
                <x-btn type="submit" variant="primary" icon="check">
                    {{ __('messages.save') }}
                </x-btn>
                <x-btn :href="route('admin.categories.index')" variant="secondary">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
