<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Services') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filters -->
                    <form method="GET" action="{{ route('services.index') }}" class="mb-8">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Search services..." 
                                value="{{ $filters['q'] ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->service_listings_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Price (ETB)</label>
                                <input 
                                    type="number" 
                                    name="min_price" 
                                    value="{{ $filters['min_price'] ?? '' }}"
                                    placeholder="0"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Price (ETB)</label>
                                <input 
                                    type="number" 
                                    name="max_price" 
                                    value="{{ $filters['max_price'] ?? '' }}"
                                    placeholder="{{ number_format($priceRange['max']) }}"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>

                            <!-- Delivery Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery (Days)</label>
                                <select name="delivery_days" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Any</option>
                                    <option value="3" {{ ($filters['delivery_days'] ?? '') == '3' ? 'selected' : '' }}>Up to 3 days</option>
                                    <option value="7" {{ ($filters['delivery_days'] ?? '') == '7' ? 'selected' : '' }}>Up to 7 days</option>
                                    <option value="14" {{ ($filters['delivery_days'] ?? '') == '14' ? 'selected' : '' }}>Up to 14 days</option>
                                    <option value="30" {{ ($filters['delivery_days'] ?? '') == '30' ? 'selected' : '' }}>Up to 30 days</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <!-- Rating Filter -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                                <select name="min_rating" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Any Rating</option>
                                    <option value="4" {{ ($filters['min_rating'] ?? '') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="4.5" {{ ($filters['min_rating'] ?? '') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="relevance" {{ ($filters['sort'] ?? '') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                    <option value="newest" {{ ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="price_low" {{ ($filters['sort'] ?? '') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ ($filters['sort'] ?? '') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="rating" {{ ($filters['sort'] ?? '') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                                    <option value="popular" {{ ($filters['sort'] ?? '') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                    <option value="delivery" {{ ($filters['sort'] ?? '') == 'delivery' ? 'selected' : '' }}>Fastest Delivery</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                    Apply Filters
                                </button>
                                <a href="{{ route('services.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Count -->
                    <div class="mb-6">
                        <p class="text-gray-600">
                            Found <span class="font-semibold">{{ $services->total() }}</span> services
                        </p>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($services as $service)
                            <x-service-card :service="$service" />
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No services found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($services->hasPages())
                        <div class="mt-8">
                            {{ $services->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
