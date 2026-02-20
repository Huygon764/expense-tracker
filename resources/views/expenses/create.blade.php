@extends('layouts.app')

@section('title', 'Add expense')

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Add expense</h1>

    @if($categories->isEmpty())
        <p class="rounded-md bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200 mb-4">
            Create a category first before adding expenses. <a href="{{ route('categories.create') }}" class="font-medium underline">Create category</a>.
        </p>
    @endif

    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4" @if($categories->isEmpty()) onsubmit="return false;" @endif>
        @csrf

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100"
                @if($categories->isEmpty()) disabled @endif>
            @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
            <select name="category_id" id="category_id" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100"
                @if($categories->isEmpty()) disabled @endif>
                <option value="">Select category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note (optional)</label>
            <input type="text" name="note" id="note" value="{{ old('note') }}"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100"
                @if($categories->isEmpty()) disabled @endif>
            @error('note')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100"
                @if($categories->isEmpty()) disabled @endif>
            @error('date')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500" @if($categories->isEmpty()) disabled @endif>Add expense</button>
            <a href="{{ route('expenses.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
