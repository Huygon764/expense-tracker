@extends('layouts.app')

@section('title', __('messages.default_categories') . ' — ' . __('messages.admin'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('messages.default_categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ {{ __('messages.add_default_category') }}</a>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.icon') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.color') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.sort_order') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-xl">{{ $category->icon ?: '—' }}</td>
                    <td class="px-4 py-3">
                        @if($category->color)
                            <div class="flex items-center gap-2">
                                <div class="h-5 w-5 rounded-full border border-gray-200 dark:border-gray-600" style="background-color: {{ $category->color }}"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $category->color }}</span>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $category->sort_order }}</td>
                    <td class="px-4 py-3 text-right flex items-center justify-end gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-md bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">{{ __('messages.edit') }}</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('{{ __('messages.confirm_delete_category', ['name' => $category->name]) }}')" class="rounded-md bg-red-50 dark:bg-red-900/20 px-3 py-1 text-xs font-medium text-red-700 dark:text-red-300 hover:bg-red-100">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
