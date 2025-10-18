<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Student Skills Marketplace') }}</title>
        <meta name="description" content="{{ $description ?? 'Connect with talented university students in Ethiopia for your service needs' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Additional Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            <!-- Navigation -->
            <x-navigation />

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="py-6">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-12">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1">
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">About</h3>
                            <p class="mt-4 text-sm text-gray-600">
                                Connecting talented Ethiopian university students with clients who need their skills.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">For Students</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">How it Works</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Success Stories</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Seller Guide</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">For Clients</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Browse Services</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">How to Order</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Buyer Protection</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">Support</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Help Center</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Contact Us</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Terms of Service</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <p class="text-sm text-gray-500 text-center">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>
