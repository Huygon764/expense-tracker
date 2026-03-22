@extends('layouts.app')

@section('title', __('messages.edit_category'))
@section('page-title', __('messages.edit_category'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h2 class="font-display text-xl font-bold text-on-surface mb-6">{{ __('messages.edit_category') }}</h2>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <x-form-input name="name" :label="__('messages.name')" :value="old('name', $category->name)" required icon="tag" />

            <div>
                <x-form-input name="icon" :label="__('messages.icon')" :value="old('icon', $category->icon)" :placeholder="__('messages.icon_placeholder')" />
                <div class="mt-2">
                    <x-emoji-picker target="icon" />
                </div>
            </div>

            <div>
                <label for="color" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ __('messages.color_hex') }}</label>
                <div class="flex gap-3 items-center">
                    <input type="color" id="color_picker" value="{{ old('color', $category->color ?? '#3b82f6') }}"
                           class="h-11 w-14 rounded-xl bg-surface-container-low cursor-pointer p-1 focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <input type="text" name="color" id="color" value="{{ old('color', $category->color ?? '#3b82f6') }}" placeholder="#3b82f6"
                           class="block flex-1 rounded-xl bg-surface-container-low text-on-surface text-sm py-3 px-4 font-mono placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-colors {{ $errors->has('color') ? 'ring-2 ring-error/30' : '' }}">
                </div>
                @error('color')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
                <x-btn variant="primary" type="submit">
                    {{ __('messages.update') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('categories.index')">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var picker = document.getElementById('color_picker');
    var input = document.getElementById('color');
    if (picker && input) {
        picker.addEventListener('input', function() { input.value = this.value; });
        input.addEventListener('input', function() { if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) picker.value = this.value; });
    }
});
</script>
@endsection
