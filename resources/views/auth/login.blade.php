@extends('layouts.guest')

@section('title', __('messages.login'))

@section('content')
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.login') }}</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember"
                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('messages.remember_me') }}</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ __('messages.forgot_password') }}</a>
        </div>
        <div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('messages.login') }}
            </button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
            {{ __('messages.login_with_google') }}
        </a>
    </div>

    <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        {{ __('messages.dont_have_account') }} <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ __('messages.register') }}</a>
    </p>
</div>
@endsection
