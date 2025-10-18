<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Find Talented Student Service Providers
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Connect with skilled university students in Ethiopia for your projects
                </p>
                
                <!-- Search Bar -->
                <form action="{{ route('services.index') }}" method="GET" class="max-w-3xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Search for services (e.g., web development, graphic design...)" 
                                class="w-full px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                value="{{ request('q') }}"
                            >
                        </div>
                        <button type="submit" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Quick Links -->
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    @foreach($categories->take(6) as $category)
                        <a href="{{ route('categories.show', $category) }}" class="px-4 py-2 bg-blue-500 bg-opacity-30 rounded-full hover:bg-opacity-50 transition text-sm">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Services -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Featured Services</h2>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                View All →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredServices as $service)
                <x-service-card :service="$service" />
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <p>No services available yet. Be the first to list your service!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- How It Works -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">How It Works</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- For Clients -->
                <div class="bg-white p-8 rounded-lg shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">1. Browse Services</h3>
                    <p class="text-gray-600">Search and filter through services offered by talented university students.</p>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">2. Place Order</h3>
                    <p class="text-gray-600">Select a service, provide requirements, and make secure payment via Stripe.</p>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">3. Get Deliverables</h3>
                    <p class="text-gray-600">Receive your work, review it, and release payment when satisfied.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600 text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="text-xl mb-8 text-blue-100">Join our marketplace today</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.student') }}" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
                    Register as Student Provider
                </a>
                <a href="{{ route('register.client') }}" class="px-8 py-4 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition border-2 border-white">
                    Register as Client
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
