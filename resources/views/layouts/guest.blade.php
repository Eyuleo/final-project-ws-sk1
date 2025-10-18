<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Student Skills Marketplace') }}</title>
        <meta name="description" content="{{ $description ?? 'Join the marketplace connecting Ethiopian university students with clients' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 via-white to-green-50">
            <!-- Guest Header -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <a href="/" class="flex items-center">
                            <x-application-logo class="h-10 w-auto fill-current text-blue-600" />
                            <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                        </a>
                        <nav class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="ml-4 text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Get Started</a>
                                    @endif
                                @endauth
                            @endif
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
                <div class="w-full sm:max-w-md">
                    <!-- Logo for centered forms -->
                    @if(!isset($hidelogo))
                        <div class="text-center mb-6">
                            <a href="/">
                                <x-application-logo class="w-16 h-16 mx-auto fill-current text-blue-600" />
                            </a>
                            <h2 class="mt-4 text-2xl font-bold text-gray-900">{{ $heading ?? 'Welcome' }}</h2>
                            @isset($subheading)
                                <p class="mt-2 text-sm text-gray-600">{{ $subheading }}</p>
                            @endisset
                        </div>
                    @endif

                    <!-- Card Content -->
                    <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg">
                        <div class="px-6 py-8">
                            {{ $slot }}
                        </div>
                    </div>

                    <!-- Additional Links -->
                    @isset($footer)
                        <div class="mt-6 text-center text-sm text-gray-600">
                            {{ $footer }}
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Guest Footer -->
            <footer class="bg-white border-t border-gray-200 mt-12">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <p class="text-sm text-gray-500">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </p>
                        <div class="flex space-x-6 mt-4 sm:mt-0">
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Terms</a>
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Privacy</a>
                            <a href="#" class="text-sm text-gray-500 hover:text-gray-900">Help</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
