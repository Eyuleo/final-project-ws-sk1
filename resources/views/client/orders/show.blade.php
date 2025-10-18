<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order #{{ $order->order_number }}
            </h2>
            <a href="{{ route('client.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
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
                                    @if($order->status === 'pending' || $order->status === 'pending_payment') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'accepted' || $order->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'completed') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'approved') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled' || $order->status === 'declined') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Payment: 
                                    <span class="font-medium @if($order->payment_status === 'paid') text-green-600 @else text-yellow-600 @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-sm text-gray-500">Total Amount</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service & Provider Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">{{ $order->serviceListing->title }}</h4>
                            <p class="text-sm text-gray-600 mb-4">{{ $order->serviceListing->description }}</p>
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
                            </div>
                        </div>
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">Provider</h4>
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-lg">
                                            {{ substr($order->studentProfile->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->studentProfile->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->studentProfile->user->email }}</p>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Requirements</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $order->requirements }}</p>
                    
                    @if($order->attachment_files)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Attachments</h4>
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
                </div>
            </div>

            <!-- Deliverables (if completed) -->
            @if($order->status === 'completed' || $order->status === 'approved')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" id="approve">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Deliverables</h3>
                        
                        @if($order->delivery_note)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Delivery Note:</p>
                                <p class="text-gray-600">{{ $order->delivery_note }}</p>
                            </div>
                        @endif

                        @if($order->deliverable_files)
                            <div class="mb-6">
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
                        @endif

                        @if($order->status === 'completed')
                            <!-- Approve/Revision Actions -->
                            <div class="flex items-center space-x-4">
                                <form method="POST" action="{{ route('client.orders.approve', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Approve & Release Payment
                                    </button>
                                </form>

                                @if($order->revision_count < $order->max_revisions)
                                    <button onclick="document.getElementById('revision-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition">
                                        Request Revision ({{ $order->max_revisions - $order->revision_count }} left)
                                    </button>
                                @else
                                    <button onclick="document.getElementById('dispute-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                                        Open Dispute
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Payment Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Platform Fee (15%):</span>
                            <span class="font-medium">${{ number_format($order->platform_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-semibold pt-2 border-t">
                            <span>Total:</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2">
                            <span class="text-gray-600">Escrow Status:</span>
                            <span class="font-medium capitalize">{{ $order->escrow_status }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Revision Request Modal -->
    <div id="revision-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Request Revision</h3>
                <form method="POST" action="{{ route('client.orders.revision', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="revision_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            What needs to be revised?
                        </label>
                        <textarea id="revision_notes" name="revision_notes" rows="4" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Please provide specific feedback..."></textarea>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            Submit Request
                        </button>
                        <button type="button" onclick="document.getElementById('revision-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dispute Modal -->
    <div id="dispute-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Open Dispute</h3>
                <form method="POST" action="{{ route('client.orders.dispute', $order) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for dispute
                        </label>
                        <textarea id="reason" name="reason" rows="4" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Explain why you're opening a dispute..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="evidence_files" class="block text-sm font-medium text-gray-700 mb-2">
                            Evidence (optional)
                        </label>
                        <input type="file" id="evidence_files" name="evidence_files[]" multiple
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Open Dispute
                        </button>
                        <button type="button" onclick="document.getElementById('dispute-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
