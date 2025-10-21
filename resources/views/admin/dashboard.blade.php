<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                                <dd class="text-xs text-gray-500">
                                    {{ $stats['total_students'] }} students, {{ $stats['total_clients'] }} clients
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_orders']) }}</dd>
                                <dd class="text-xs text-gray-500">
                                    {{ $stats['completed_orders'] }} completed
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Services -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Services</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['active_services']) }}</dd>
                                <dd class="text-xs text-gray-500">
                                    of {{ $stats['total_services'] }} total
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Platform Revenue</dt>
                                <dd class="text-lg font-semibold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</dd>
                                <dd class="text-xs text-gray-500">
                                    From platform fees
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disputed Orders Alert -->
        @if($stats['disputed_orders'] > 0)
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <strong>{{ $stats['disputed_orders'] }}</strong> order(s) requiring dispute resolution.
                            <a href="{{ route('admin.disputes.index') }}" class="font-medium underline">View disputes</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Orders</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        @forelse($recentOrders as $order)
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $order->serviceListing->title }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->order_number }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $order->clientProfile->user->name }} → {{ $order->studentProfile->user->name }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <x-order-status :status="$order->status" />
                                    <span class="text-sm font-medium text-gray-900 mt-1">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No recent orders</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Disputed Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Disputed Orders</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        @forelse($disputedOrders as $order)
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $order->serviceListing->title }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->order_number }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Opened {{ $order->disputed_at?->diffForHumans() ?? $order->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <a href="{{ route('admin.disputes.show', $order) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                        Review
                                    </a>
                                    <span class="text-sm font-medium text-gray-900 mt-1">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No disputed orders</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Trend (if data available) -->
        @if($revenueTrend->count() > 0)
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Revenue Trend (Last 7 Days)</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-end space-x-2 h-32">
                        @foreach($revenueTrend as $day)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-500 rounded-t" style="height: {{ ($day->total / $revenueTrend->max('total')) * 100 }}%"></div>
                                <span class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</span>
                                <span class="text-xs font-medium text-gray-700">${{ number_format($day->total, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
