<h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('messages.set_budget') }}</h1>
<p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.set_budget_help') }}</p>

<form method="POST" action="{{ route('onboarding.storeStep2') }}" class="space-y-4">
    @csrf
    <div>
        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.period') }}</label>
        <select name="type" id="type" required
            class="mt-1 block w-full max-w-xs rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
            <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
        </select>
        @error('type')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.amount') }}</label>
        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" step="0.01"
            class="mt-1 block w-full max-w-xs rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
        @error('amount')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex gap-3 pt-2">
        <a href="{{ route('onboarding.step1') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('messages.back') }}</a>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">{{ __('messages.finish') }}</button>
    </div>
</form>
