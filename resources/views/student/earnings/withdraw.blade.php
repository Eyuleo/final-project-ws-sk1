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
                        <!-- Stripe Connect Warning -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800 mb-2">Connect Your Stripe Account</p>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        To receive automatic withdrawals directly to your bank account, you need to connect your Stripe account first.
                                    </p>
                                    <a href="{{ route('student.earnings.connect-stripe') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Connect Stripe Now
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
                    @else
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

                            <!-- Withdrawal Method -->
                            <div class="mb-6">
                                <label class="block font-medium text-sm text-gray-700 mb-2">
                                    Withdrawal Method <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input 
                                            type="radio" 
                                            name="method" 
                                            value="bank_transfer"
                                            {{ old('method') === 'bank_transfer' ? 'checked' : '' }}
                                            class="mt-1 rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            onchange="toggleMethodFields('bank_transfer')"
                                        >
                                        <div class="ml-3 flex-1">
                                            <span class="font-medium text-gray-900">Bank Transfer</span>
                                            <p class="text-sm text-gray-500">Direct transfer to your bank account (2-3 business days)</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input 
                                            type="radio" 
                                            name="method" 
                                            value="mobile_money"
                                            {{ old('method') === 'mobile_money' ? 'checked' : '' }}
                                            class="mt-1 rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            onchange="toggleMethodFields('mobile_money')"
                                        >
                                        <div class="ml-3 flex-1">
                                            <span class="font-medium text-gray-900">Mobile Money</span>
                                            <p class="text-sm text-gray-500">Transfer to mobile money account (1-2 business days)</p>
                                        </div>
                                    </label>
                                </div>
                                @error('method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Transfer Fields -->
                            <div id="bank-transfer-fields" class="space-y-4 mb-6" style="display: {{ old('method') === 'bank_transfer' ? 'block' : 'none' }};">
                                <div>
                                    <label for="bank_name" class="block font-medium text-sm text-gray-700 mb-2">
                                        Bank Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="bank_name" 
                                        id="bank_name" 
                                        value="{{ old('bank_name') }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                        placeholder="e.g., Commercial Bank of Ethiopia"
                                    >
                                    @error('bank_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="account_number" class="block font-medium text-sm text-gray-700 mb-2">
                                        Account Number <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="account_number" 
                                        id="account_number" 
                                        value="{{ old('account_number') }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                        placeholder="Enter your account number"
                                    >
                                    @error('account_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="account_holder_name" class="block font-medium text-sm text-gray-700 mb-2">
                                        Account Holder Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="account_holder_name" 
                                        id="account_holder_name" 
                                        value="{{ old('account_holder_name', Auth::user()->name) }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                        placeholder="Full name as it appears on your account"
                                    >
                                    @error('account_holder_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile Money Fields -->
                            <div id="mobile-money-fields" class="space-y-4 mb-6" style="display: {{ old('method') === 'mobile_money' ? 'block' : 'none' }};">
                                <div>
                                    <label for="mobile_provider" class="block font-medium text-sm text-gray-700 mb-2">
                                        Mobile Provider <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        name="mobile_provider" 
                                        id="mobile_provider"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                    >
                                        <option value="">Select provider</option>
                                        <option value="telebirr" {{ old('mobile_provider') === 'telebirr' ? 'selected' : '' }}>TeleBirr</option>
                                        <option value="mpesa" {{ old('mobile_provider') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                        <option value="other" {{ old('mobile_provider') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('mobile_provider')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone_number" class="block font-medium text-sm text-gray-700 mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="phone_number" 
                                        id="phone_number" 
                                        value="{{ old('phone_number') }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                        placeholder="+251912345678"
                                    >
                                    @error('phone_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Important Notice -->
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Important Information</h4>
                                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                    <li>Processing time: 2-3 business days for bank transfers, 1-2 days for mobile money</li>
                                    <li>A 2% processing fee will be deducted (minimum $1.00)</li>
                                    <li>Ensure your account details are correct to avoid delays</li>
                                    <li>You'll receive a notification once the withdrawal is processed</li>
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

    @push('scripts')
    <script>
        function toggleMethodFields(method) {
            const bankFields = document.getElementById('bank-transfer-fields');
            const mobileFields = document.getElementById('mobile-money-fields');

            if (method === 'bank_transfer') {
                bankFields.style.display = 'block';
                mobileFields.style.display = 'none';
            } else if (method === 'mobile_money') {
                bankFields.style.display = 'none';
                mobileFields.style.display = 'block';
            }
        }

        // Initialize on page load if method is already selected
        document.addEventListener('DOMContentLoaded', function() {
            const selectedMethod = document.querySelector('input[name="method"]:checked');
            if (selectedMethod) {
                toggleMethodFields(selectedMethod.value);
            }
        });
    </script>
    @endpush
</x-app-layout>
