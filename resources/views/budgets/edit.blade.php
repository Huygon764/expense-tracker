@extends('layouts.app')

@section('page-title', __('messages.edit_budget'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.edit_budget') }}</h1>

        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <x-form-select name="type" :label="__('messages.period')" :required="true">
                <option value="weekly" {{ old('type', $budget->type) === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                <option value="monthly" {{ old('type', $budget->type) === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            </x-form-select>

            <x-amount-input name="amount" :label="__('messages.amount')" :value="$budget->amount" :required="true" />

            <div class="flex gap-3 pt-2">
                <x-btn variant="primary" type="submit" icon="check">
                    {{ __('messages.update') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('budgets.index')" icon="arrow-left">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
