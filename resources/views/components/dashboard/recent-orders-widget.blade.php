@props(['orders'])

<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            <a href="{{ route(auth()->user()->role . '.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                View all
            </a>
        </div>
        
        @if($orders->isEmpty())
            <p class="text-sm text-gray-500 text-center py-4">No recent orders</p>
        @else
            <div class="space-y-3">
                @foreach($orders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $order->serviceListing->title ?? 'Order #' . $order->order_number }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $order->created_at->diffForHumans() }}
                            </p>
                        </div>
                        
                        <div class="ml-4 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            
                            <a href="{{ route(auth()->user()->role . '.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
