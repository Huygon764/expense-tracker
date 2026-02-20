@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Categories</h1>
    <a href="{{ route('categories.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
        Create category
    </a>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Icon</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Color</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($categories as $category)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-lg">{{ $category->icon ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($category->color)
                            <span class="inline-block w-6 h-6 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $category->color }}"></span>
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $category->color }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline ml-4" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No categories yet. <a href="{{ route('categories.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Create one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
