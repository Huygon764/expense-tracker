<h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Choose your categories</h1>
<p class="text-gray-600 dark:text-gray-400 mb-6">Select at least one category to track your expenses.</p>

<form method="POST" action="{{ route('onboarding.storeStep1') }}" class="space-y-4">
    @csrf
    <div class="space-y-3">
        @foreach($defaultCategories as $dc)
            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                <input type="checkbox" name="default_category_ids[]" value="{{ $dc->id }}"
                    {{ in_array($dc->id, old('default_category_ids', [])) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <span class="text-xl">{{ $dc->icon ?? '📦' }}</span>
                <span class="font-medium">{{ $dc->name }}</span>
                @if($dc->color)
                    <span class="inline-block w-4 h-4 rounded flex-shrink-0" style="background-color: {{ $dc->color }}"></span>
                @endif
            </label>
        @endforeach
    </div>
    @error('default_category_ids')
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @if($errors->has('default_category_ids.*'))
        <p class="text-sm text-red-600 dark:text-red-400">Please select at least one category.</p>
    @endif
    <div class="pt-4">
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Next</button>
    </div>
</form>
