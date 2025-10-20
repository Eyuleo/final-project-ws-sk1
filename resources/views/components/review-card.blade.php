@props(['review'])

<div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center space-x-3">
            <!-- Reviewer Profile Picture -->
            <div class="flex-shrink-0">
                @if($review->reviewer->clientProfile?->profile_picture)
                    <img src="{{ Storage::url($review->reviewer->clientProfile->profile_picture) }}" 
                         alt="{{ $review->reviewer->name }}"
                         class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600 font-semibold text-lg">
                            {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Reviewer Name and Date -->
            <div>
                <h4 class="font-semibold text-gray-900">{{ $review->reviewer->name }}</h4>
                <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Rating Stars -->
        <div class="flex items-center">
            <x-rating-stars :rating="$review->rating" size="sm" />
            <span class="ml-2 text-sm font-medium text-gray-700">{{ $review->rating }}.0</span>
        </div>
    </div>

    <!-- Review Text -->
    @if($review->review_text)
        <div class="mb-3">
            <p class="text-gray-700 leading-relaxed">{{ $review->review_text }}</p>
        </div>
    @endif

    <!-- Review Tags -->
    @if($review->tags && count($review->tags) > 0)
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($review->tags as $tag)
                @php
                    $tagLabels = [
                        'professional' => 'Professional',
                        'responsive' => 'Responsive',
                        'quality' => 'High Quality',
                        'communication' => 'Great Communication',
                        'timely' => 'On Time',
                        'creative' => 'Creative',
                        'exceeded_expectations' => 'Exceeded Expectations'
                    ];
                    $label = $tagLabels[$tag] ?? ucfirst(str_replace('_', ' ', $tag));
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ $label }}
                </span>
            @endforeach
        </div>
    @endif

    <!-- Service Info (if available) -->
    @if($review->order && $review->order->serviceListing)
        <div class="mt-3 pt-3 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Service: 
                <a href="{{ route('services.show', $review->order->serviceListing) }}" 
                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ $review->order->serviceListing->title }}
                </a>
            </p>
        </div>
    @endif
</div>
