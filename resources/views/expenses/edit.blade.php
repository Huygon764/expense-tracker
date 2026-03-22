@extends('layouts.app')

@section('page-title', __('messages.edit_expense'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.edit_expense') }}</h1>

        <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <x-amount-input name="amount" :label="__('messages.amount')" :value="$expense->amount" :required="true" />

            <x-form-select name="category_id" :label="__('messages.category')" :required="true">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </x-form-select>

            <x-form-input name="note" :label="__('messages.note_optional')" :value="old('note', $expense->note)" />

            <x-form-input name="date" type="date" :label="__('messages.date')" :value="old('date', $expense->date->format('Y-m-d'))" :required="true" icon="calendar" />

            <div class="flex gap-3 pt-2">
                <x-btn variant="primary" type="submit" icon="check">
                    {{ __('messages.update') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('expenses.index')" icon="arrow-left">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
