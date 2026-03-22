@extends('layouts.app')

@section('page-title', __('messages.edit_goal'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                <x-icon name="target" class="w-5 h-5 text-primary" />
            </div>
            <h1 class="font-display text-xl font-bold text-on-surface">{{ __('messages.edit_goal') }}</h1>
        </div>

        <form method="POST" action="{{ route('savings-goals.update', $savingsGoal) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <x-form-input
                name="name"
                :label="__('messages.name')"
                :value="old('name', $savingsGoal->name)"
                :required="true"
                maxlength="255"
            />

            <x-amount-input name="target_amount" :label="__('messages.target_amount')" :value="$savingsGoal->target_amount" :required="true" />

            <x-form-input
                name="deadline"
                type="date"
                :label="__('messages.deadline')"
                :value="old('deadline', $savingsGoal->deadline->format('Y-m-d'))"
                :required="true"
            />

            <div class="flex items-center gap-3 pt-2">
                <x-btn variant="primary" type="submit">
                    {{ __('messages.update') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('savings-goals.index')">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
