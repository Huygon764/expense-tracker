@extends('layouts.guest')

@section('title', __('messages.reset_password'))

@section('content')
    <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.reset_password') }}</h1>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form-input name="email" type="email" :label="__('messages.email')" :value="old('email', $email)" required autofocus autocomplete="username" icon="mail" />
        <x-form-input name="password" type="password" :label="__('messages.password')" required autocomplete="new-password" icon="lock" />
        <x-form-input name="password_confirmation" type="password" :label="__('messages.confirm_password')" required autocomplete="new-password" icon="lock" />
        <x-btn type="submit" variant="primary" class="w-full">{{ __('messages.reset_password') }}</x-btn>
    </form>
@endsection
