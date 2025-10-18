<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-8 mb-6 text-white">
            <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-blue-100">Find talented student service providers for your projects</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Orders</p>
                            <p class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Completed</p>
                            <p class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Messages</p>
                            <p class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Saved</p>
                            <p class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('services.index') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">Browse Services</p>
                            <p class="text-sm text-gray-500">Find service providers</p>
                        </div>
                    </a>

                    <a href="{{ route('categories.index') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">Browse Categories</p>
                            <p class="text-sm text-gray-500">Explore by category</p>
                        </div>
                    </a>

                    <a href="#" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">My Orders</p>
                            <p class="text-sm text-gray-500">View order history</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity / Getting Started -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Getting Started -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Getting Started</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 font-semibold">1</div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Browse Services</p>
                                <p class="text-sm text-gray-500">Explore our marketplace to find the perfect service for your needs</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 font-semibold">2</div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Place an Order</p>
                                <p class="text-sm text-gray-500">Select a service and provide your requirements</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 font-semibold">3</div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Communicate</p>
                                <p class="text-sm text-gray-500">Chat with your service provider and track progress</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 font-semibold">4</div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Receive & Review</p>
                                <p class="text-sm text-gray-500">Get your completed work and leave a review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Categories -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Categories</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('categories.show', 'web_development') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Web Development</p>
                        </a>
                        <a href="{{ route('categories.show', 'graphic_design') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Graphic Design</p>
                        </a>
                        <a href="{{ route('categories.show', 'content_writing') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Content Writing</p>
                        </a>
                        <a href="{{ route('categories.show', 'video_editing') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Video Editing</p>
                        </a>
                        <a href="{{ route('categories.show', 'digital_marketing') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Digital Marketing</p>
                        </a>
                        <a href="{{ route('categories.show', 'tutoring') }}" class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                            <p class="font-medium text-gray-900 text-sm">Tutoring</p>
                        </a>
                    </div>
                    <a href="{{ route('categories.index') }}" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All Categories →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
