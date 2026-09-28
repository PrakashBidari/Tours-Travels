@php
    $notifications = Auth::user()->notifications()->latest()->take(8)->get();
    $unreadCount = Auth::user()->unreadNotifications()->count();
@endphp

<div x-data="{ unreadCount: {{ $unreadCount }} }" @app-notification.window="unreadCount++">
    <x-dropdown align="right" width="80">
        <x-slot name="trigger">
            <button type="button" class="relative p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span x-show="unreadCount > 0" x-cloak class="absolute -top-1 -right-1 flex h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-white dark:ring-gray-800" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="flex items-center justify-between px-4 py-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Notifications</span>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Mark all read</button>
                    </form>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <a
                        href="{{ $notification->data['url'] ?? '#' }}"
                        class="block px-4 py-2.5 text-sm border-t border-gray-100 dark:border-gray-700 {{ $notification->read_at ? 'text-gray-500 dark:text-gray-400' : 'text-gray-800 dark:text-gray-100 bg-indigo-50/50 dark:bg-indigo-500/10' }} hover:bg-gray-50 dark:hover:bg-gray-700/40"
                    >
                        <p>{{ $notification->data['message'] ?? 'Notification' }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <p class="px-4 py-6 text-sm text-center text-gray-400">No notifications yet.</p>
                @endforelse
            </div>
        </x-slot>
    </x-dropdown>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        try {
            if (! window.Echo) {
                return;
            }

            window.Echo.private('App.Models.User.{{ Auth::id() }}').notification((notification) => {
                window.AppAlert?.info(notification.message ?? 'You have a new notification.', 'New notification');
                window.dispatchEvent(new CustomEvent('app-notification', { detail: notification }));
            });
        } catch (error) {
            console.error('Live notifications unavailable:', error);
        }
    });
</script>
