<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dispute Resolution') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('admin.disputes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Order</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               placeholder="Order number..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Filter -->
                    <div>
                        <label for="filter" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="filter" 
                                id="filter"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Disputes</option>
                            <option value="unresolved" {{ request('filter') === 'unresolved' ? 'selected' : '' }}>Unresolved</option>
                            <option value="resolved" {{ request('filter') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Disputes List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    Disputed Orders ({{ $disputes->total() }})
                </h3>
            </div>

            @if($disputes->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($disputes as $dispute)
                        <li class="px-4 py-5 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Disputed
                                        </span>
                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $dispute->serviceListing->title }}
                                        </h4>
                                    </div>
                                    
                                    <div class="mt-2 flex flex-col sm:flex-row sm:space-x-4">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            Order: {{ $dispute->order_number }}
                                        </p>
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            Client: {{ $dispute->clientProfile->user->name }}
                                        </p>
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            Student: {{ $dispute->studentProfile->user->name }}
                                        </p>
                                    </div>

                                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Amount: ${{ number_format($dispute->total_amount, 2) }}</span>
                                        <span>•</span>
                                        <span>Opened {{ $dispute->disputed_at?->diffForHumans() ?? $dispute->updated_at->diffForHumans() }}</span>
                                        @if($dispute->dispute_resolved_at)
                                            <span>•</span>
                                            <span class="text-green-600">Resolved {{ $dispute->dispute_resolved_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ route('admin.disputes.show', $dispute) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination -->
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                    {{ $disputes->links('pagination::tailwind') }}
                </div>
            @else
                <div class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No disputes found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('search') || request('filter'))
                            Try adjusting your filters
                        @else
                            All orders are running smoothly
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
