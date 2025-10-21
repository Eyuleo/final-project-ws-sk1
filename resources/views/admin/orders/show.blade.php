<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order: {{ $order->order_number }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Order Details</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Service</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->serviceListing->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Client</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $order->clientProfile->user->name }}
                                    <span class="text-gray-500">({{ $order->clientProfile->user->email }})</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student Provider</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $order->studentProfile->user->name }}
                                    <span class="text-gray-500">({{ $order->studentProfile->user->email }})</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <x-order-status :status="$order->status" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Escrow Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                        {{ $order->escrow_status === 'held' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($order->escrow_status === 'released' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ str_replace('_', ' ', $order->escrow_status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Requirements</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $order->requirements }}</dd>
                            </div>
                            @if($order->deadline)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->deadline)->format('M d, Y') }}</dd>
                                </div>
                            @endif
                            @if($order->deliverable_files)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Deliverables</dt>
                                    <dd class="mt-1">
                                        @foreach($order->deliverable_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-blue-600 hover:text-blue-500 text-sm block">
                                                📎 {{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif
                            @if($order->delivery_note)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Delivery Note</dt>
                                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-gray-50 p-3 rounded">{{ $order->delivery_note }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Messages -->
                @if($order->messages->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Messages ({{ $order->messages->count() }})</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($order->messages as $message)
                                    <div class="flex {{ $message->sender_id === $order->clientProfile->user_id ? 'justify-start' : 'justify-end' }}">
                                        <div class="max-w-xs lg:max-w-md">
                                            <div class="text-xs text-gray-500 mb-1">
                                                {{ $message->sender->name }} - {{ $message->created_at->format('M d, Y H:i') }}
                                            </div>
                                            <div class="rounded-lg px-4 py-2 {{ $message->sender_id === $order->clientProfile->user_id ? 'bg-gray-100' : 'bg-blue-100' }}">
                                                <p class="text-sm text-gray-900">{{ $message->message }}</p>
                                                @if($message->attachment_path)
                                                    <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-500 mt-1 block">
                                                        📎 {{ basename($message->attachment_path) }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Transactions -->
                @if($order->transactions->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Transaction History</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($order->transactions as $transaction)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900">${{ number_format($transaction->amount, 2) }}</td>
                                                <td class="px-3 py-2 text-sm">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize
                                                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Order Summary</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Subtotal:</dt>
                                <dd class="text-sm font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Platform Fee (15%):</dt>
                                <dd class="text-sm font-medium text-gray-900">${{ number_format($order->platform_fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-200">
                                <dt class="text-base font-medium text-gray-900">Total:</dt>
                                <dd class="text-base font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Timeline</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm text-gray-900">Order Created</p>
                                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($order->accepted_at)
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm text-gray-900">Order Accepted</p>
                                                    <p class="text-xs text-gray-500">{{ $order->accepted_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($order->completed_at)
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm text-gray-900">Work Completed</p>
                                                    <p class="text-xs text-gray-500">{{ $order->completed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($order->approved_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-600 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm text-gray-900">Order Approved</p>
                                                    <p class="text-xs text-gray-500">{{ $order->approved_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if($order->status === 'disputed')
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <a href="{{ route('admin.disputes.show', $order) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                View Dispute Details
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
