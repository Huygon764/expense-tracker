@extends('layouts.app')

@section('page-title', __('messages.manage_categories'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-display font-bold text-on-surface">{{ __('messages.default_categories') }}</h1>
        <x-btn :href="route('admin.categories.create')" variant="primary" icon="plus">
            {{ __('messages.add_default_category') }}
        </x-btn>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <x-card class="!p-4 border-l-4 border-l-emerald-500">
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </x-card>
    @endif
    @if(session('error'))
        <x-card class="!p-4 border-l-4 border-l-error">
            <p class="text-sm text-error">{{ session('error') }}</p>
        </x-card>
    @endif

    {{-- Category list --}}
    <div class="space-y-3">
        @forelse($categories as $category)
            <x-card class="!p-4">
                <div class="flex items-center gap-4">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center shrink-0 text-xl">
                        {{ $category->icon ?: '--' }}
                    </div>

                    {{-- Name --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-on-surface">{{ $category->name }}</p>
                    </div>

                    {{-- Color swatch --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if($category->color)
                            <div class="h-6 w-6 rounded-lg" style="background-color: {{ $category->color }}"></div>
                            <span class="text-xs text-on-surface-variant font-mono hidden sm:inline">{{ $category->color }}</span>
                        @else
                            <span class="text-xs text-on-surface-variant">--</span>
                        @endif
                    </div>

                    {{-- Sort order --}}
                    <div class="shrink-0">
                        <x-badge color="secondary" size="xs">{{ $category->sort_order }}</x-badge>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <x-btn :href="route('admin.categories.edit', $category)" variant="ghost" size="sm" icon="edit">
                            {{ __('messages.edit') }}
                        </x-btn>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" data-confirm="{{ __('messages.confirm_delete_category', ['name' => $category->name]) }}">
                            @csrf
                            @method('DELETE')
                            <x-btn
                                type="submit"
                                variant="danger"
                                size="sm"
                                icon="trash"
                            >
                                {{ __('messages.delete') }}
                            </x-btn>
                        </form>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto mb-4">
                    <x-icon name="tag" class="w-7 h-7 text-on-surface-variant" />
                </div>
                <p class="text-on-surface-variant font-medium">{{ __('messages.no_data') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
