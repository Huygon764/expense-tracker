@extends('layouts.app')

@section('title', 'Edit category')

@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Edit category</h1>

    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon (emoji or text)</label>
            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" placeholder="e.g. 🍔"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            @error('icon')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color (hex)</label>
            <div class="mt-1 flex gap-2 items-center">
                <input type="color" id="color_picker" value="{{ old('color', $category->color ?? '#3b82f6') }}"
                    class="h-10 w-14 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                <input type="text" name="color" id="color" value="{{ old('color', $category->color ?? '#3b82f6') }}" placeholder="#3b82f6"
                    class="block flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100">
            </div>
            @error('color')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Update</button>
            <a href="{{ route('categories.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var picker = document.getElementById('color_picker');
    var input = document.getElementById('color');
    if (picker && input) {
        picker.addEventListener('input', function() { input.value = this.value; });
        input.addEventListener('input', function() { if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) picker.value = this.value; });
    }
});
</script>
@endsection