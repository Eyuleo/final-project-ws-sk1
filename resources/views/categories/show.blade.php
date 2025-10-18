<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $category->name }}
            </h2>
            <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800">
                ← All Categories
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Category Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if($category->icon)
                            <div class="text-5xl">{{ $category->icon }}</div>
                        @endif
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                            @if($category->description)
                                <p class="text-gray-600">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Services -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('categories.show', $category) }}" class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
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

                            <!-- Rating Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                                <select name="min_rating" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Any Rating</option>
                                    <option value="4" {{ ($filters['min_rating'] ?? '') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="4.5" {{ ($filters['min_rating'] ?? '') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4 items-end">
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
                                <a href="{{ route('categories.show', $category) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Count -->
                    <div class="mb-6">
                        <p class="text-gray-600">
                            Found <span class="font-semibold">{{ $services->total() }}</span> services in {{ $category->name }}
                        </p>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($services as $service)
                            <x-service-card :service="$service" />
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No services found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later</p>
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

            <!-- Other Categories -->
            @if($allCategories->count() > 1)
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Explore Other Categories</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($allCategories->where('id', '!=', $category->id) as $otherCategory)
                                <a href="{{ route('categories.show', $otherCategory) }}" class="px-4 py-2 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-full text-sm transition">
                                    {{ $otherCategory->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
