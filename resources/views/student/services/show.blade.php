<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Service Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('student.services.edit', $service) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Service
                </a>
                <a href="{{ route('student.services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Services
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <div class="mt-1">
                        @if($service->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Paused
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_orders'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">In Progress</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['in_progress_orders'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-500">Rating</p>
                    <div class="flex items-center mt-1">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="ml-1 text-lg font-semibold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</span>
                        <span class="ml-1 text-sm text-gray-500">({{ $stats['total_reviews'] }})</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Service Details -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <!-- Category Badge -->
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide mb-2">
                            {{ $service->category->name ?? 'Uncategorized' }}
                        </p>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $service->title }}</h1>

                        <!-- Description -->
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">About This Service</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $service->description }}</p>
                        </div>

                        <!-- Tags -->
                        @if($service->tags && count($service->tags) > 0)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service->tags as $tag)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Requirements -->
                        @if($service->requirements)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Requirements from Buyer</h3>
                                <p class="text-gray-700 whitespace-pre-line">{{ $service->requirements }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Portfolio Samples -->
                @if($service->portfolio_files && count($service->portfolio_files) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Samples</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($service->portfolio_files as $sample)
                                    <div class="relative group cursor-pointer">
                                        @if(str_starts_with($sample['type'], 'image/'))
                                            <img src="{{ Storage::url($sample['thumbnail'] ?? $sample['path']) }}" alt="{{ $sample['original_name'] }}" class="w-full h-40 object-cover rounded-lg shadow-md group-hover:shadow-xl transition">
                                        @else
                                            <div class="w-full h-40 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-xl transition">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if(isset($sample['description']))
                                            <p class="mt-2 text-sm text-gray-600">{{ $sample['description'] }}</p>
                                        @endif
                                        <a href="{{ Storage::url($sample['path']) }}" target="_blank" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 rounded-lg transition flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                @if($service->reviews->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reviews</h3>
                            <div class="space-y-4">
                                @foreach($service->reviews as $review)
                                    <div class="border-b border-gray-200 pb-4 last:border-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $review->client?->name ?? 'Anonymous Client' }}</p>
                                                <x-rating-stars :rating="$review->rating" size="sm" />
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg sticky top-6">
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <p class="text-4xl font-bold text-gray-900">${{ number_format($service->price, 2) }}</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $service->delivery_days }} day{{ $service->delivery_days > 1 ? 's' : '' }} delivery
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                {{ $service->revisions ?? 2 }} revision{{ ($service->revisions ?? 2) != 1 ? 's' : '' }}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <form method="POST" action="{{ route('student.services.toggle-status', $service) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white {{ $service->status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} transition">
                                    @if($service->status === 'active')
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pause Service
                                    @else
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Activate Service
                                    @endif
                                </button>
                            </form>

                            <a href="{{ route('student.services.edit', $service) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Service
                            </a>

                            <form method="POST" action="{{ route('student.services.destroy', $service) }}" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md font-semibold text-sm text-red-700 bg-white hover:bg-red-50 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete Service
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-500">Created</p>
                                <p class="font-medium text-gray-900">{{ $service->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Last Updated</p>
                                <p class="font-medium text-gray-900">{{ $service->updated_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Category</p>
                                <p class="font-medium text-gray-900">{{ $service->category->name ?? 'Uncategorized' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
