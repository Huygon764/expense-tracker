@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-xl">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Profile</h1>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
            <p class="mt-1 block w-full rounded-md border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 px-3 py-2 text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
        </div>
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="email_notification" value="1" {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Email notifications</span>
            </label>
        </div>
        <div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
