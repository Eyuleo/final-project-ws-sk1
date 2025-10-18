<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Placed Successfully') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Success Icon -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                        <p class="text-gray-600">Your order has been placed and payment is held securely in escrow.</p>
                    </div>

                    <!-- Order Details -->
                    <div class="border-t border-b border-gray-200 py-6 mb-6">
                        <h4 class="font-semibold text-lg mb-4">Order Details</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-medium">{{ $order->order_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span class="font-medium">{{ $order->serviceListing->title }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Provider:</span>
                                <span class="font-medium">{{ $order->studentProfile->user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Deadline:</span>
                                <span class="font-medium">{{ $order->deadline->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Acceptance
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold mb-3">Payment Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Platform Fee:</span>
                                <span>${{ number_format($order->platform_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                                <span>Total Paid:</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-blue-900 mb-2">What happens next?</h4>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>The provider will be notified and has 48 hours to accept your order.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Your payment is held securely in escrow and will only be released when you approve the work.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>You can communicate with the provider through the order messaging system.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>You'll receive email notifications about order status updates.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('client.orders.show', $order) }}" class="flex-1 px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            View Order Details
                        </a>
                        <a href="{{ route('client.dashboard') }}" class="flex-1 px-6 py-3 bg-white text-gray-700 text-center font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Confirmation Notice -->
            <div class="mt-4 text-center text-sm text-gray-600">
                <p>A confirmation email has been sent to {{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
