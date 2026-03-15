@extends('layouts.app')

@section('title', __('messages.edit_budget'))

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('messages.edit_budget') }}</h1>

    <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.period') }}</label>
            <select name="type" id="type" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="weekly" {{ old('type', $budget->type) === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                <option value="monthly" {{ old('type', $budget->type) === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <x-amount-input name="amount" :label="__('messages.amount')" :value="$budget->amount" :required="true" />

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">{{ __('messages.update') }}</button>
            <a href="{{ route('budgets.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
