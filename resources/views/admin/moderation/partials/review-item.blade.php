<div class="flex items-start justify-between">
    <div class="flex-1">
        <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">Review by {{ $review->reviewer->name }}</h3>
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                Hidden
            </span>
        </div>
        
        <div class="mt-2 text-sm text-gray-600">
            <p>{{ $review->comment }}</p>
        </div>

        <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Order: {{ $review->order->order_number }}
            </div>
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                For: {{ $review->reviewed->name }}
            </div>
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $review->created_at->format('M d, Y') }}
            </div>
        </div>
    </div>

    <div class="ml-4 flex-shrink-0 flex space-x-2">
        <form method="POST" action="{{ route('admin.moderation.reviews.show', $review) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Make Visible
            </button>
        </form>

        <form method="POST" action="{{ route('admin.moderation.reviews.hide', $review) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Keep Hidden
            </button>
        </form>
    </div>
</div>
