<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $service->title }}
            </h2>
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Services
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Details Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Category Badge -->
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $service->category->name }}
                                </span>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $service->title }}</h1>

                            <!-- Rating and Stats -->
                            <div class="flex items-center gap-6 mb-6 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <x-rating-stars :rating="$service->average_rating" />
                                    <span class="ml-2 font-medium">{{ number_format($service->average_rating, 1) }}</span>
                                    <span class="ml-1">({{ $stats['total_orders'] }} orders)</span>
                                </div>
                                <div>
                                    <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $service->views_count }} views
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="prose max-w-none mb-6">
                                <h3 class="text-lg font-semibold mb-2">Description</h3>
                                <p class="text-gray-700 whitespace-pre-line">{{ $service->description }}</p>
                            </div>

                            <!-- Requirements -->
                            @if($service->requirements)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">Requirements</h3>
                                    <p class="text-gray-700 whitespace-pre-line">{{ $service->requirements }}</p>
                                </div>
                            @endif

                            <!-- Portfolio Files -->
                            @if($service->portfolio_files && count($service->portfolio_files) > 0)
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Portfolio Samples</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($service->portfolio_files as $file)
                                            <div class="border rounded-lg overflow-hidden">
                                                @if(str_ends_with($file, '.jpg') || str_ends_with($file, '.jpeg') || str_ends_with($file, '.png') || str_ends_with($file, '.gif'))
                                                    <img src="{{ Storage::url($file) }}" alt="Portfolio sample" class="w-full h-32 object-cover">
                                                @else
                                                    <div class="w-full h-32 bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Provider Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">About the Provider</h3>
                            <div class="flex items-start gap-4">
                                @if($service->studentProfile->profile_picture)
                                    <img src="{{ Storage::url($service->studentProfile->profile_picture) }}" alt="{{ $service->studentProfile->user->name }}" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-xl">
                                        {{ substr($service->studentProfile->user->name, 0, 1) }}
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $service->studentProfile->user->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $service->studentProfile->university }}</p>
                                    
                                    @if($service->studentProfile->bio)
                                        <p class="mt-2 text-gray-700">{{ Str::limit($service->studentProfile->bio, 150) }}</p>
                                    @endif

                                    <div class="mt-3 flex items-center gap-4 text-sm text-gray-600">
                                        <div>
                                            <x-rating-stars :rating="$service->studentProfile->average_rating" size="sm" />
                                            <span class="ml-1">{{ number_format($service->studentProfile->average_rating, 1) }}</span>
                                        </div>
                                        <div>{{ $service->studentProfile->total_orders }} orders completed</div>
                                    </div>

                                    <a href="{{ route('student.profile.public', $service->studentProfile) }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View Full Profile →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-bold text-gray-900">{{ number_format($service->price) }}</span>
                                    <span class="text-gray-600">ETB</span>
                                </div>
                                @if($service->pricing_model === 'hourly')
                                    <span class="text-sm text-gray-500">per hour</span>
                                @endif
                            </div>

                            <div class="space-y-3 mb-6 text-sm">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $service->delivery_days }} day{{ $service->delivery_days > 1 ? 's' : '' }} delivery</span>
                                </div>
                            </div>

                            @auth
                                @if(auth()->user()->role === 'client')
                                    <a href="{{ route('client.orders.create', $service) }}" class="block w-full px-6 py-3 bg-blue-600 text-white text-center font-semibold rounded-lg hover:bg-blue-700 transition">
                                        Order Now
                                    </a>
                                @else
                                    <div class="text-center text-gray-600 py-3">
                                        Only clients can place orders
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-blue-600 text-white text-center font-semibold rounded-lg hover:bg-blue-700 transition">
                                    Login to Order
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            @if(isset($reviews) && $reviews->count() > 0)
                <div class="mt-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900">Reviews ({{ $stats['total_reviews'] ?? 0 }})</h3>
                                <div class="flex items-center">
                                    <x-rating-stars :rating="$service->average_rating" size="md" />
                                    <span class="ml-2 text-lg font-medium text-gray-700">{{ number_format($service->average_rating, 1) }}</span>
                                </div>
                            </div>

                            <!-- Review List -->
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <x-review-card :review="$review" />
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($reviews->hasPages())
                                <div class="mt-6">
                                    {{ $reviews->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif(isset($reviews))
                <div class="mt-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Reviews</h3>
                            <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to order and review this service!</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Services -->
            @if($relatedServices->isNotEmpty())
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Related Services</h3>
                    <div class="grid grid-cols-1 mx-2 md:grid-cols-2 md:mx-0 lg:grid-cols-4 lg:mx-0 gap-6 ">
                        @foreach($relatedServices as $relatedService)
                            <x-service-card :service="$relatedService" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
