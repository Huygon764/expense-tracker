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
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="email_notification" value="1" {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Email notifications</span>
            </label>
        </div>
        <div>
            <label for="monthly_income" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly income</label>
            <input id="monthly_income" type="number" name="monthly_income" value="{{ old('monthly_income', $user->monthly_income) }}" step="0.01" min="0" placeholder="0.00"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('monthly_income')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
