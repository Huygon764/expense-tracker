@extends('layouts.app')

@section('page-title', __('messages.notifications'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-display font-bold text-on-surface">{{ __('messages.notifications') }}</h1>
        @if($notifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                @csrf
                @method('PATCH')
                <x-btn type="submit" variant="secondary" size="sm" icon="check">
                    {{ __('messages.mark_all_read') }}
                </x-btn>
            </form>
        @endif
    </div>

    {{-- Notification list --}}
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $typeIconMap = [
                    'budget_alert' => ['icon' => 'alert-triangle', 'bg' => 'bg-tertiary/10 text-tertiary'],
                    'info' => ['icon' => 'info', 'bg' => 'bg-primary/10 text-primary'],
                    'warning' => ['icon' => 'alert-triangle', 'bg' => 'bg-tertiary/10 text-tertiary'],
                    'error' => ['icon' => 'x', 'bg' => 'bg-error/10 text-error'],
                ];
                $typeInfo = $typeIconMap[$notification->type] ?? ['icon' => 'bell', 'bg' => 'bg-primary/10 text-primary'];
            @endphp
            <x-card class="!p-4 flex items-start gap-4 {{ !$notification->is_read ? 'border-l-4 border-l-primary' : '' }}">
                {{-- Icon circle --}}
                <div class="w-10 h-10 rounded-xl {{ $typeInfo['bg'] }} flex items-center justify-center shrink-0">
                    <x-icon :name="$typeInfo['icon']" class="w-5 h-5" />
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-on-surface {{ !$notification->is_read ? 'font-semibold' : '' }}">{{ $notification->message }}</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-xs text-on-surface-variant">{{ $notification->created_at->diffForHumans() }}</span>
                        <x-badge :color="$notification->is_read ? 'secondary' : 'primary'" size="xs">
                            {{ $notification->is_read ? __('messages.read_badge') : $notification->type }}
                        </x-badge>
                    </div>
                </div>

                {{-- Action --}}
                <div class="shrink-0">
                    @if(!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <x-btn type="submit" variant="ghost" size="sm">
                                {{ __('messages.mark_read') }}
                            </x-btn>
                        </form>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto mb-4">
                    <x-icon name="bell" class="w-7 h-7 text-on-surface-variant" />
                </div>
                <p class="text-on-surface-variant font-medium">{{ __('messages.no_notifications') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
