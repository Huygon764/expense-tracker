@extends('layouts.guest')

@section('title', __('messages.forgot_password'))

@section('content')
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.forgot_password') }}</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.forgot_password_help') }}</p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('messages.send_reset_link') }}
            </button>
        </div>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ __('messages.back_to_login') }}</a>
    </p>
</div>
@endsection
