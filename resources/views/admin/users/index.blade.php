@extends('layouts.app')

@section('page-title', __('messages.manage_users'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-display font-bold text-on-surface">{{ __('messages.manage_users_title') }}</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card
            :label="__('messages.total_users')"
            :value="$totalUsers"
            icon="user"
            color="primary"
        />
        <x-stat-card
            :label="__('messages.active_users')"
            :value="$activeUsers"
            icon="check"
            color="primary"
        />
        <x-stat-card
            :label="__('messages.disabled_users')"
            :value="$disabledUsers"
            icon="x"
            color="error"
        />
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <x-card class="!p-4 border-l-4 border-l-emerald-500">
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </x-card>
    @endif
    @if(session('error'))
        <x-card class="!p-4 border-l-4 border-l-error">
            <p class="text-sm text-error">{{ session('error') }}</p>
        </x-card>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
        <div class="flex-1">
            <x-form-input
                name="search"
                :value="$search"
                :placeholder="__('messages.search') . '...'"
                icon="search"
            />
        </div>
        <x-btn type="submit" variant="primary" icon="search">
            {{ __('messages.search') }}
        </x-btn>
        @if($search)
            <x-btn :href="route('admin.users.index')" variant="secondary">
                {{ __('messages.clear_filter') }}
            </x-btn>
        @endif
    </form>

    {{-- User list --}}
    <div class="space-y-3">
        @forelse($users as $user)
            <x-card class="!p-4">
                <div class="flex items-center gap-4">
                    {{-- Avatar initials --}}
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-on-surface truncate">{{ $user->name }}</p>
                        <p class="text-xs text-on-surface-variant truncate">{{ $user->email }}</p>
                    </div>

                    {{-- Date --}}
                    <div class="hidden sm:block text-xs text-on-surface-variant shrink-0">
                        {{ $user->created_at->format('d/m/Y') }}
                    </div>

                    {{-- Status badge --}}
                    <div class="shrink-0">
                        @if($user->is_active)
                            <x-badge color="success">{{ __('messages.active') }}</x-badge>
                        @else
                            <x-badge color="error">{{ __('messages.inactive') }}</x-badge>
                        @endif
                    </div>

                    {{-- Toggle action --}}
                    <div class="shrink-0">
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            @if($user->is_active)
                                <x-btn
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                    onclick="return confirm('{{ __('messages.confirm_disable_account', ['name' => $user->name]) }}')"
                                >
                                    {{ __('messages.disable') }}
                                </x-btn>
                            @else
                                <x-btn
                                    type="submit"
                                    variant="primary"
                                    size="sm"
                                    onclick="return confirm('{{ __('messages.confirm_enable_account', ['name' => $user->name]) }}')"
                                >
                                    {{ __('messages.enable') }}
                                </x-btn>
                            @endif
                        </form>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto mb-4">
                    <x-icon name="user" class="w-7 h-7 text-on-surface-variant" />
                </div>
                <p class="text-on-surface-variant font-medium">
                    {{ $search ? __('messages.no_users_found') : __('messages.no_users_yet') }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif
</div>
@endsection
