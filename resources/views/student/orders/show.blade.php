<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order #{{ $order->order_number }}
            </h2>
            <a href="{{ route('student.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Order Status Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Order Status</h3>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'accepted' || $order->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'completed') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'approved') bg-green-100 text-green-800
                                    @elseif($order->status === 'revision_requested') bg-orange-100 text-orange-800
                                    @elseif($order->status === 'declined') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                                @if($order->revision_count > 0)
                                    <span class="text-sm text-gray-500">
                                        Revisions: {{ $order->revision_count }}/{{ $order->max_revisions }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($order->subtotal, 2) }}</p>
                            <p class="text-sm text-gray-500">Your Earnings</p>
                            <p class="text-xs text-gray-400 mt-1">Total: ${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($order->status === 'pending')
                        <div class="mt-6 flex items-center space-x-4">
                            <form method="POST" action="{{ route('student.orders.accept', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                    Accept Order
                                </button>
                            </form>
                            <button onclick="document.getElementById('decline-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                                Decline Order
                            </button>
                        </div>
                    @endif

                    @if($order->status === 'accepted')
                        <div class="mt-6">
                            <form method="POST" action="{{ route('student.orders.start', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                    Start Working
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(in_array($order->status, ['accepted', 'in_progress', 'revision_requested']))
                        <div class="mt-6">
                            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                Upload Deliverables
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Service & Client Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">Service</h4>
                            <p class="text-sm text-gray-600 mb-4">{{ $order->serviceListing->title }}</p>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Category:</span>
                                    <span class="font-medium">{{ $order->serviceListing->category->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Unit Price:</span>
                                    <span class="font-medium">${{ number_format($order->unit_price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Quantity:</span>
                                    <span class="font-medium">{{ $order->quantity }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Subtotal:</span>
                                    <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">Client</h4>
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-lg">
                                            {{ substr($order->clientProfile->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->clientProfile->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->clientProfile->user->email }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Order Date:</span>
                                    <span class="font-medium">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Deadline:</span>
                                    <span class="font-medium">{{ $order->deadline->format('M d, Y') }}</span>
                                </div>
                                @if($order->accepted_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Accepted:</span>
                                    <span class="font-medium">{{ $order->accepted_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                                @if($order->completed_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Completed:</span>
                                    <span class="font-medium">{{ $order->completed_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Requirements</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $order->requirements }}</p>
                    
                    @if($order->attachment_files)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Client Attachments</h4>
                            <div class="space-y-2">
                                @foreach($order->attachment_files as $file)
                                    <a href="{{ $file }}" target="_blank" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        {{ basename($file) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($order->revision_notes)
                        <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <h4 class="text-sm font-medium text-orange-900 mb-2">Revision Request</h4>
                            <p class="text-sm text-orange-700">{{ $order->revision_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Deliverables (if submitted) -->
            @if($order->deliverable_files)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Deliverables</h3>
                        
                        @if($order->delivery_note)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Delivery Note:</p>
                                <p class="text-gray-600">{{ $order->delivery_note }}</p>
                            </div>
                        @endif

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Files</h4>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($order->deliverable_files as $file)
                                    <a href="{{ $file }}" target="_blank" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ basename($file) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        @if($order->status === 'completed')
                            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                <p class="text-sm text-purple-700">
                                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Waiting for client approval. Payment will be released once approved.
                                </p>
                            </div>
                        @endif

                        @if($order->status === 'approved')
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-700">
                                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Order approved! Payment has been released to your account.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Decline Order Modal -->
    <div id="decline-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Decline Order</h3>
                <form method="POST" action="{{ route('student.orders.decline', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="decline_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for declining
                        </label>
                        <textarea id="decline_reason" name="decline_reason" rows="4" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Please provide a reason..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">The client will be refunded automatically.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Decline Order
                        </button>
                        <button type="button" onclick="document.getElementById('decline-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Deliverables Modal -->
    <div id="upload-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Deliverables</h3>
                <form method="POST" action="{{ route('student.orders.upload', $order) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="deliverables" class="block text-sm font-medium text-gray-700 mb-2">
                            Deliverable Files *
                        </label>
                        <input type="file" id="deliverables" name="deliverables[]" multiple required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Upload 1-10 files, max 50MB each</p>
                    </div>
                    <div class="mb-4">
                        <label for="delivery_note" class="block text-sm font-medium text-gray-700 mb-2">
                            Delivery Note (optional)
                        </label>
                        <textarea id="delivery_note" name="delivery_note" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add any notes about the deliverables..."></textarea>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Upload & Complete
                        </button>
                        <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
