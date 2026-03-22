@extends('layouts.guest')

@section('title', __('messages.register'))

@section('content')
    <h1 class="font-display text-2xl font-bold text-on-surface mb-1">{{ __('messages.register') }}</h1>
    <p class="text-sm text-on-surface-variant mb-6">{{ __('messages.create_account_subtitle') ?? 'Create your account' }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-form-input name="name" :label="__('messages.name')" :value="old('name')" required autofocus autocomplete="name" icon="user" />
        <x-form-input name="email" type="email" :label="__('messages.email')" :value="old('email')" required autocomplete="username" icon="mail" />
        <x-form-input name="password" type="password" :label="__('messages.password')" required autocomplete="new-password" icon="lock" />
        <x-form-input name="password_confirmation" type="password" :label="__('messages.confirm_password')" required autocomplete="new-password" icon="lock" />

        <x-btn type="submit" variant="primary" class="w-full">{{ __('messages.register') }}</x-btn>
    </form>

    <p class="mt-6 text-center text-sm text-on-surface-variant">
        {{ __('messages.already_have_account') }}
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-container transition-colors">{{ __('messages.login') }}</a>
    </p>
@endsection
