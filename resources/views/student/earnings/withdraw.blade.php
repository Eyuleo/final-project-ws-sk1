<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Request Withdrawal') }}
            </h2>
            <a href="{{ route('student.earnings.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Earnings
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Balance Summary -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Available Balance</h3>
                        <p class="text-3xl font-bold text-green-600">${{ number_format($profile->available_balance, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Minimum withdrawal: $10.00</p>
                    </div>

                    @if(!$hasStripeAccount)
                        <!-- Stripe Connect Required -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-yellow-800 mb-2">Stripe Connect Required</p>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        All withdrawals are processed through Stripe Connect. You must connect your Stripe account to receive automatic payouts directly to your bank account.
                                    </p>
                                    <a href="{{ route('student.earnings.connect-stripe') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.548 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.594-7.305h.003z"/>
                                        </svg>
                                        Connect Stripe Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif(!$stripeVerified)
                        <!-- Stripe Not Verified -->
                        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-orange-800 mb-2">Complete Stripe Verification</p>
                                    <p class="text-sm text-orange-700 mb-3">
                                        {{ $stripeMessage }}
                                    </p>
                                    <a href="{{ route('student.earnings.connect-stripe') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Complete Verification
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($profile->available_balance < 10)
                        <!-- Insufficient Balance Warning -->
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-red-800">Insufficient Balance</p>
                                    <p class="text-sm text-red-700">You need at least $10.00 to request a withdrawal.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($hasStripeAccount && $stripeVerified)
                        <!-- Withdrawal Form -->
                        <form method="POST" action="{{ route('student.earnings.withdraw.store') }}">
                            @csrf

                            <!-- Amount -->
                            <div class="mb-6">
                                <label for="amount" class="block font-medium text-sm text-gray-700 mb-2">
                                    Withdrawal Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input 
                                        type="number" 
                                        name="amount" 
                                        id="amount" 
                                        step="0.01"
                                        min="10"
                                        max="{{ $profile->available_balance }}"
                                        value="{{ old('amount') }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pl-7"
                                        placeholder="0.00"
                                        required
                                    >
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Processing fee: 2% (minimum $1.00)
                                </p>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Withdrawal Method Info -->
                            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.548 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.594-7.305h.003z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-sm text-gray-900 mb-1">Withdrawal Method: Stripe Connect</p>
                                        <p class="text-sm text-gray-600">Funds will be transferred directly to your connected bank account via Stripe.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Important Notice -->
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Important Information</h4>
                                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                    <li>Payouts are processed via Stripe Connect to your connected bank account</li>
                                    <li>Processing time: 2-7 business days (depending on your bank)</li>
                                    <li>A 2% processing fee will be deducted (minimum $1.00)</li>
                                    <li>You'll receive a notification once the payout is processed</li>
                                </ul>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('student.earnings.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Submit Withdrawal Request
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
