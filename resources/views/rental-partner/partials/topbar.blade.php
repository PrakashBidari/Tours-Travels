<header class="h-16 flex items-center gap-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 shrink-0">
    <button type="button" @click="sidebarOpen = true" class="lg:hidden -ml-1 p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700/60">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" /></svg>
    </button>

    <div class="flex-1"></div>

    <x-notifications-bell />

    <div class="relative">
        <x-dropdown align="right" width="56">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <img class="h-9 w-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-700" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    <span class="hidden md:flex md:flex-col md:items-start">
                        <span class="font-medium text-gray-700 dark:text-gray-200 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-400 leading-tight">{{ Auth::user()->company_name }}</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="hidden md:block h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
