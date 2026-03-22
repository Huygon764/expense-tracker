@extends('layouts.app')

@section('page-title', __('messages.profile'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">{{ __('messages.edit_profile') }}</h1>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Left: Personal Identity --}}
            <x-card>
                <h2 class="text-base font-display font-bold text-on-surface mb-5">{{ __('messages.personal_identity') ?? 'Personal Identity' }}</h2>
                <div class="space-y-4">
                    <x-form-input
                        name="name"
                        :label="__('messages.name')"
                        :value="$user->name"
                        :required="true"
                        icon="user"
                        autofocus
                    />

                    <x-form-input
                        name="email"
                        :label="__('messages.email')"
                        :value="$user->email"
                        :readonly="true"
                        icon="lock"
                    />
                </div>

                <div class="mt-6">
                    <x-btn type="submit" variant="primary" icon="check">
                        {{ __('messages.save') }}
                    </x-btn>
                </div>
            </x-card>

            {{-- Right: Preferences --}}
            <x-card>
                <h2 class="text-base font-display font-bold text-on-surface mb-5">{{ __('messages.preferences') ?? 'Preferences' }}</h2>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="email_notification" value="1"
                            {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="w-11 h-6 rounded-full bg-surface-container-high peer-checked:bg-primary transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full bg-surface-container-lowest shadow-editorial-sm transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-on-surface">{{ __('messages.email_notifications') }}</span>
                        <p class="text-xs text-on-surface-variant mt-0.5">{{ __('messages.email_notifications_hint') ?? 'Receive budget alerts and reports via email' }}</p>
                    </div>
                </label>
            </x-card>
        </div>
    </form>
</div>
@endsection
