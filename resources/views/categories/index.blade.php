@extends('layouts.app')

@section('title', __('messages.categories'))
@section('page-title', __('messages.categories'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.categories') }}</h2>
        <p class="mt-1 text-sm text-on-surface-variant">{{ __('messages.manage_your_categories') ?? __('messages.categories') }}</p>
    </div>
    <x-btn variant="primary" :href="route('categories.create')" icon="plus">
        {{ __('messages.add_category') }}
    </x-btn>
</div>

@if($categories->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
            <x-card class="relative group flex flex-col gap-4">
                <div class="flex items-start gap-4">
                    {{-- Icon circle with category color --}}
                    <div class="flex items-center justify-center w-12 h-12 rounded-full text-xl shrink-0"
                         style="background-color: {{ $category->color ?? '#0058be' }}20;">
                        <span>{{ $category->icon ?? '---' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-display text-base font-semibold text-on-surface truncate">{{ $category->name }}</h3>
                        @if($category->color)
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="inline-block w-4 h-4 rounded-full shrink-0" style="background-color: {{ $category->color }}"></span>
                                <span class="text-xs text-on-surface-variant font-mono">{{ $category->color }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Color accent bar --}}
                @if($category->color)
                    <div class="h-1 rounded-full w-full" style="background-color: {{ $category->color }}20;">
                        <div class="h-1 rounded-full w-1/2" style="background-color: {{ $category->color }};"></div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-1" style="border-top: 1px solid rgba(191,201,200,0.15);">
                    <x-btn variant="ghost" size="sm" :href="route('categories.edit', $category)" icon="edit">
                        {{ __('messages.edit') }}
                    </x-btn>
                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" data-confirm="{{ __('messages.confirm_delete') }}">
                        @csrf
                        @method('DELETE')
                        <x-btn variant="ghost" size="sm" type="submit" icon="trash" class="text-error hover:text-error">
                            {{ __('messages.delete') }}
                        </x-btn>
                    </form>
                </div>
            </x-card>
        @endforeach

        {{-- Add new placeholder card --}}
        <a href="{{ route('categories.create') }}"
           class="flex items-center justify-center gap-3 rounded-2xl p-6 transition-colors hover:bg-surface-container-low"
           style="border: 2px dashed rgba(191,201,200,0.4);">
            <x-icon name="plus" class="w-5 h-5 text-on-surface-variant" />
            <span class="text-sm font-semibold text-on-surface-variant">{{ __('messages.add_category') }}</span>
        </a>
    </div>
@else
    {{-- Empty state --}}
    <x-card class="flex flex-col items-center justify-center py-16 text-center">
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-surface-container mb-4">
            <x-icon name="tag" class="w-8 h-8 text-on-surface-variant" />
        </div>
        <h3 class="font-display text-lg font-semibold text-on-surface mb-1">{{ __('messages.no_categories_yet') }}</h3>
        <p class="text-sm text-on-surface-variant mb-6">{{ __('messages.create_one') }}</p>
        <x-btn variant="primary" :href="route('categories.create')" icon="plus">
            {{ __('messages.add_category') }}
        </x-btn>
    </x-card>
@endif
@endsection
