@extends('layouts.app')

@section('title', 'Edit budget')

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Edit budget</h1>

    <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
            <select name="type" id="type" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="weekly" {{ old('type', $budget->type) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ old('type', $budget->type) === 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
            <input type="number" name="amount" id="amount" value="{{ old('amount', $budget->amount) }}" required min="0" step="0.01"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scope</label>
            <select name="category_id" id="category_id"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="">Total (all categories)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $budget->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Update</button>
            <a href="{{ route('budgets.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
