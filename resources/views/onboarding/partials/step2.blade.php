<h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Monthly income</h1>
<p class="text-gray-600 dark:text-gray-400 mb-6">Optional: set your monthly income for budget insights.</p>

<form method="POST" action="{{ route('onboarding.storeStep2') }}" class="space-y-4">
    @csrf
    <div>
        <label for="monthly_income" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly income</label>
        <input type="number" name="monthly_income" id="monthly_income" value="{{ old('monthly_income', $user->monthly_income) }}" min="0" step="0.01" placeholder="0"
            class="mt-1 block w-full max-w-xs rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
        @error('monthly_income')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex gap-3 pt-2">
        <a href="{{ route('onboarding.step1') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Back</a>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Next</button>
    </div>
</form>
