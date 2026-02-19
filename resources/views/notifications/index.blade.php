@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Notifications</h1>
    @if($notifications->isNotEmpty())
        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="rounded-md bg-gray-200 dark:bg-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500">
                Đánh dấu tất cả đã đọc
            </button>
        </form>
    @endif
</div>

<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Message</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <tr class="bg-white dark:bg-gray-800 {{ $notification->is_read ? 'opacity-75' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $notification->message }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $notification->type }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-right text-sm">
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Đã đọc</button>
                            </form>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">Đã đọc</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Chưa có thông báo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
