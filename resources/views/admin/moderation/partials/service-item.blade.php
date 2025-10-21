<div class="flex items-start justify-between">
    <div class="flex-1">
        <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">{{ $service->title }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                Pending Review
            </span>
        </div>
        
        <div class="mt-2 text-sm text-gray-600">
            <p class="line-clamp-2">{{ $service->description }}</p>
        </div>

        <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ $service->studentProfile->user->name }}
            </div>
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ $service->category->name }}
            </div>
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ${{ number_format($service->price, 2) }}
            </div>
            <div class="flex items-center">
                <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $service->created_at->format('M d, Y') }}
            </div>
        </div>
    </div>

    <div class="ml-4 flex-shrink-0 flex space-x-2">
        <form method="POST" action="{{ route('admin.moderation.services.approve', $service) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Approve
            </button>
        </form>

        <button type="button" 
                onclick="openRejectModal({{ $service->id }})" 
                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Reject
        </button>
    </div>
</div>

<!-- Reject Modal (place at bottom of page) -->
<div id="rejectModal-{{ $service->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form method="POST" action="{{ route('admin.moderation.services.reject', $service) }}">
                @csrf
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Reject Service
                        </h3>
                        <div class="mt-2">
                            <label for="reason-{{ $service->id }}" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                            <textarea name="reason" id="reason-{{ $service->id }}" rows="4" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Explain why this service is being rejected..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reject
                    </button>
                    <button type="button" onclick="closeRejectModal({{ $service->id }})" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(id) {
        document.getElementById('rejectModal-' + id).classList.remove('hidden');
    }
    function closeRejectModal(id) {
        document.getElementById('rejectModal-' + id).classList.add('hidden');
    }
</script>
@endpush
