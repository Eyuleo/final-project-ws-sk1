<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave a Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Order Information -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Order Details</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Order Number:</span> {{ $order->order_number }}</p>
                            <p><span class="font-medium">Service:</span> {{ $order->serviceListing->title }}</p>
                            <p><span class="font-medium">Provider:</span> {{ $order->studentProfile->user->name }}</p>
                            <p><span class="font-medium">Completed:</span> {{ $order->approved_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form method="POST" action="{{ route('client.reviews.store') }}">
                        @csrf

                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <!-- Rating -->
                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Rating <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1" id="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                data-rating="{{ $i }}"
                                                class="star-button text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-150">
                                            ★
                                        </button>
                                    @endfor
                                </div>
                                <span id="rating-text" class="text-sm text-gray-600 ml-3"></span>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Review Text -->
                        <div class="mb-6">
                            <label for="review_text" class="block font-medium text-sm text-gray-700 mb-2">
                                Your Review (Optional)
                            </label>
                            <textarea 
                                name="review_text" 
                                id="review_text" 
                                rows="5" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                placeholder="Share your experience working with this provider..."
                            >{{ old('review_text') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Minimum 20 characters if you want to include a written review.
                            </p>
                            @error('review_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                Tags (Optional - Select up to 5)
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $availableTags = [
                                        'professional' => 'Professional',
                                        'responsive' => 'Responsive',
                                        'quality' => 'High Quality',
                                        'communication' => 'Great Communication',
                                        'timely' => 'On Time',
                                        'creative' => 'Creative',
                                        'exceeded_expectations' => 'Exceeded Expectations'
                                    ];
                                @endphp
                                @foreach ($availableTags as $value => $label)
                                    <label class="inline-flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="tags[]" 
                                            value="{{ $value }}"
                                            {{ in_array($value, old('tags', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('client.orders.show', $order) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Rating stars interaction
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-button');
            const ratingInput = document.getElementById('rating-input');
            const ratingText = document.getElementById('rating-text');
            let selectedRating = parseInt(ratingInput.value) || 0;

            const ratingLabels = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };

            function updateStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
                
                if (rating > 0) {
                    ratingText.textContent = ratingLabels[rating];
                } else {
                    ratingText.textContent = '';
                }
            }

            // Initialize with old value if exists
            if (selectedRating > 0) {
                updateStars(selectedRating);
            }

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.dataset.rating);
                    ratingInput.value = selectedRating;
                    updateStars(selectedRating);
                });

                star.addEventListener('mouseenter', function() {
                    const hoverRating = parseInt(this.dataset.rating);
                    updateStars(hoverRating);
                });
            });

            document.getElementById('rating-stars').addEventListener('mouseleave', function() {
                updateStars(selectedRating);
            });
        });
    </script>
    @endpush
</x-app-layout>
