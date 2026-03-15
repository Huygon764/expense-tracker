@extends('layouts.app')

@section('title', 'Thêm danh mục mặc định — Admin')

@section('content')
<div class="max-w-lg space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Danh sách</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Thêm danh mục mặc định</h1>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon (emoji)</label>
            <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="🍔"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('icon') border-red-400 @enderror">
            @error('icon')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Màu</label>
            <div class="flex items-center gap-3">
                <input type="color" id="color" name="color" value="{{ old('color', '#6366f1') }}"
                    class="h-10 w-16 cursor-pointer rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-1">
                <span class="text-xs text-gray-500 dark:text-gray-400">Chọn màu cho danh mục</span>
            </div>
            @error('color')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('sort_order')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Thêm danh mục</button>
            <a href="{{ route('admin.categories.index') }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Hủy</a>
        </div>
    </form>
</div>
@endsection
