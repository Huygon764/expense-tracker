@extends('layouts.app')

@section('title', 'Add recurring expense')

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Add recurring expense</h1>

    <form method="POST" action="{{ route('recurring-expenses.store') }}" class="space-y-4" id="recurring-form">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('title')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
            <select name="category_id" id="category_id"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="">No category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
            <select name="type" id="type" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="weekly" {{ old('type', 'monthly') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div id="day-of-week-wrap" style="display: {{ old('type', 'monthly') === 'weekly' ? 'block' : 'none' }};">
            <label for="day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day of week</label>
            <select name="day_of_week" id="day_of_week"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                <option value="0" {{ old('day_of_week') === '0' ? 'selected' : '' }}>Sunday</option>
                <option value="1" {{ old('day_of_week') === '1' ? 'selected' : '' }}>Monday</option>
                <option value="2" {{ old('day_of_week') === '2' ? 'selected' : '' }}>Tuesday</option>
                <option value="3" {{ old('day_of_week') === '3' ? 'selected' : '' }}>Wednesday</option>
                <option value="4" {{ old('day_of_week') === '4' ? 'selected' : '' }}>Thursday</option>
                <option value="5" {{ old('day_of_week') === '5' ? 'selected' : '' }}>Friday</option>
                <option value="6" {{ old('day_of_week') === '6' ? 'selected' : '' }}>Saturday</option>
            </select>
            @error('day_of_week')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div id="day-of-month-wrap" style="display: {{ old('type', 'monthly') === 'monthly' ? 'block' : 'none' }};">
            <label for="day_of_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day of month</label>
            <select name="day_of_month" id="day_of_month"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ old('day_of_month', 1) == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endfor
            </select>
            @error('day_of_month')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Add recurring</button>
            <a href="{{ route('recurring-expenses.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    var isWeekly = this.value === 'weekly';
    document.getElementById('day-of-week-wrap').style.display = isWeekly ? 'block' : 'none';
    document.getElementById('day-of-month-wrap').style.display = isWeekly ? 'none' : 'block';
});
</script>
@endsection
