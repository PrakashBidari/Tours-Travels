<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <x-flash-alerts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-gray-50">

            <!-- Decorative panel -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600">
                <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-violet-400/20 blur-3xl"></div>

                <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                    <a href="/" class="flex items-center gap-2.5">
                        <x-application-mark :light="true" class="h-9 w-auto" />
                        <span class="text-xl font-semibold">{{ config('app.name', 'Booking') }}</span>
                    </a>

                    <div class="max-w-md">
                        <h1 class="text-4xl font-bold leading-tight">Your journey, our commitment.</h1>
                        <p class="mt-4 text-indigo-100 text-lg">Tour packages, flights, bus tickets, car rental, hotels and visa services &mdash; all in one place.</p>

                        <ul class="mt-8 space-y-3">
                            @foreach ([
                                'Nepal & international tour packages',
                                'Track and manage your bookings online',
                                'Personal support at every step',
                            ] as $feature)
                                <li class="flex items-center gap-3 text-indigo-50">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </span>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="text-sm text-indigo-200">&copy; {{ date('Y') }} {{ config('app.name', 'Booking') }}. All rights reserved.</p>
                </div>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex flex-col">
                <div class="flex items-center justify-between p-6 lg:justify-end">
                    <a href="/" class="flex items-center gap-2 lg:hidden">
                        <x-application-mark class="h-8 w-auto" />
                        <span class="font-semibold text-gray-800">{{ config('app.name', 'Booking') }}</span>
                    </a>
                    <a href="/" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        &larr; Back to homepage
                    </a>
                </div>

                <div class="flex-1 flex flex-col justify-center items-center px-6 py-6 sm:px-10">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
