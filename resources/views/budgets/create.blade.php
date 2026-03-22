@extends('layouts.app')

@section('page-title', __('messages.add_budget'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.add_budget') }}</h1>

        <form method="POST" action="{{ route('budgets.store') }}" class="space-y-5">
            @csrf

            <x-form-select name="type" :label="__('messages.period')" :required="true">
                <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            </x-form-select>

            <x-amount-input name="amount" :label="__('messages.amount')" :required="true" />

            <div class="flex gap-3 pt-2">
                <x-btn variant="primary" type="submit" icon="check">
                    {{ __('messages.add_budget') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('budgets.index')" icon="arrow-left">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
