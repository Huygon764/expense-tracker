@extends('layouts.app')

@section('page-title', __('messages.add_expense'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.add_expense') }}</h1>

        @if($categories->isEmpty())
            <div class="rounded-xl bg-tertiary-container p-4 text-sm text-tertiary mb-6">
                {{ __('messages.create_category_first') }}
                <a href="{{ route('categories.create') }}" class="font-semibold underline">{{ __('messages.add_category') }}</a>.
            </div>
        @endif

        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-5" @if($categories->isEmpty()) onsubmit="return false;" @endif>
            @csrf

            <x-amount-input name="amount" :label="__('messages.amount')" :required="true" :disabled="$categories->isEmpty()" />

            <x-form-select name="category_id" :label="__('messages.category')" :required="true" :disabled="$categories->isEmpty()">
                <option value="">{{ __('messages.select_category') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </x-form-select>

            <x-form-input name="note" :label="__('messages.note_optional')" :value="old('note')" :disabled="$categories->isEmpty()" />

            <x-form-input name="date" type="date" :label="__('messages.date')" :value="old('date', date('Y-m-d'))" :required="true" :disabled="$categories->isEmpty()" icon="calendar" />

            <div class="flex gap-3 pt-2">
                <x-btn variant="primary" type="submit" icon="check" :disabled="$categories->isEmpty()">
                    {{ __('messages.add_expense') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('expenses.index')" icon="arrow-left">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
