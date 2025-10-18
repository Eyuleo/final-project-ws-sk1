@props(['service'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <!-- Service Image -->
    <div class="relative h-48 bg-gray-200">
        @if($service->portfolio_images && count($service->portfolio_images) > 0)
            <img src="{{ Storage::url($service->portfolio_images[0]) }}" 
                 alt="{{ $service->title }}" 
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-blue-200">
                <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
        
        <!-- Status Badge -->
        @if($service->status === 'paused')
            <div class="absolute top-2 right-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Paused
                </span>
            </div>
        @endif
    </div>

    <!-- Service Content -->
    <div class="p-4">
        <!-- Category -->
        <div class="flex items-center justify-between mb-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $service->category->name }}
            </span>
            @if($service->is_featured)
                <span class="inline-flex items-center text-yellow-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </span>
            @endif
        </div>

        <!-- Title -->
        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
            <a href="{{ route('services.show', $service) }}">
                {{ $service->title }}
            </a>
        </h3>

        <!-- Description -->
        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
            {{ $service->description }}
        </p>

        <!-- Student Info -->
        <div class="flex items-center mb-4">
            @if($service->student->studentProfile->profile_picture)
                <img src="{{ Storage::url($service->student->studentProfile->profile_picture) }}" 
                     alt="{{ $service->student->name }}" 
                     class="h-8 w-8 rounded-full object-cover">
            @else
                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                    {{ substr($service->student->name, 0, 1) }}
                </div>
            @endif
            <div class="ml-2">
                <p class="text-sm font-medium text-gray-900">{{ $service->student->name }}</p>
                <div class="flex items-center">
                    <x-rating-stars :rating="$service->student->studentProfile->average_rating ?? 0" size="sm" />
                    <span class="ml-1 text-xs text-gray-500">
                        ({{ $service->student->studentProfile->total_reviews ?? 0 }})
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $service->delivery_time }} days
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Starting at</p>
                <p class="text-lg font-bold text-gray-900">
                    ${{ number_format($service->price, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
