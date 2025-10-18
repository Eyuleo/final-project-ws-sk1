<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('client.orders.index') }}" 
                       class="@if(!request('status')) border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        All Orders
                    </a>
                    <a href="{{ route('client.orders.index', ['status' => 'pending']) }}" 
                       class="@if(request('status') === 'pending') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pending
                    </a>
                    <a href="{{ route('client.orders.index', ['status' => 'in_progress']) }}" 
                       class="@if(request('status') === 'in_progress') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        In Progress
                    </a>
                    <a href="{{ route('client.orders.index', ['status' => 'completed']) }}" 
                       class="@if(request('status') === 'completed') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Completed
                    </a>
                    <a href="{{ route('client.orders.index', ['status' => 'approved']) }}" 
                       class="@if(request('status') === 'approved') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Approved
                    </a>
                </nav>
            </div>

            @if($orders->isEmpty())
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by browsing services and placing your first order.</p>
                        <div class="mt-6">
                            <a href="{{ route('client.services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Browse Services
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Orders List -->
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Order Header -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $order->serviceListing->title }}
                                                </h3>
                                                <p class="text-sm text-gray-500">Order #{{ $order->order_number }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                                <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>

                                        <!-- Order Details -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500">Provider</p>
                                                <p class="text-sm font-medium text-gray-900">{{ $order->studentProfile->user->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Deadline</p>
                                                <p class="text-sm font-medium text-gray-900">{{ $order->deadline->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Status</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($order->status === 'pending' || $order->status === 'pending_payment') bg-yellow-100 text-yellow-800
                                                    @elseif($order->status === 'accepted' || $order->status === 'in_progress') bg-blue-100 text-blue-800
                                                    @elseif($order->status === 'completed') bg-purple-100 text-purple-800
                                                    @elseif($order->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($order->status === 'cancelled' || $order->status === 'declined') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Payment</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Requirements Preview -->
                                        <div class="mb-4">
                                            <p class="text-xs text-gray-500 mb-1">Requirements</p>
                                            <p class="text-sm text-gray-700">{{ Str::limit($order->requirements, 150) }}</p>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('client.orders.show', $order) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                View Details
                                            </a>

                                            @if($order->status === 'completed')
                                                <a href="{{ route('client.orders.show', $order) }}#approve" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Review & Approve
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
