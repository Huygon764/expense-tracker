@extends('layouts.guest')

@section('title', __('messages.forgot_password'))

@section('content')
    <h1 class="font-display text-2xl font-bold text-on-surface mb-1">{{ __('messages.forgot_password') }}</h1>
    <p class="text-sm text-on-surface-variant mb-6">{{ __('messages.forgot_password_help') }}</p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-form-input name="email" type="email" :label="__('messages.email')" :value="old('email')" required autofocus icon="mail" />
        <x-btn type="submit" variant="primary" class="w-full">{{ __('messages.send_reset_link') }}</x-btn>
    </form>

    <p class="mt-6 text-center text-sm text-on-surface-variant">
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-container transition-colors">{{ __('messages.back_to_login') }}</a>
    </p>
@endsection
