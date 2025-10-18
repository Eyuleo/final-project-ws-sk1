<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Place Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Service Summary -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">{{ $service->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($service->description, 200) }}</p>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Provider:</span>
                                <span class="font-medium">{{ $service->studentProfile->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Price:</span>
                                <span class="font-medium">${{ number_format($service->price, 2) }}</span>
                                @if($service->pricing_model === 'hourly')
                                    <span class="text-gray-500">/hour</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-gray-600">Delivery Time:</span>
                                <span class="font-medium">{{ $service->delivery_days }} days</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Category:</span>
                                <span class="font-medium">{{ $service->category->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Form -->
                    <form method="POST" action="{{ route('client.orders.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="service_listing_id" value="{{ $service->id }}">

                        <!-- Requirements -->
                        <div class="mb-6">
                            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">
                                Project Requirements <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="requirements" 
                                name="requirements" 
                                rows="6" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('requirements') border-red-500 @enderror"
                                placeholder="Please provide detailed requirements for your project (minimum 50 characters)..."
                            >{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Be as specific as possible to help the provider deliver exactly what you need.</p>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                min="1" 
                                max="100"
                                value="{{ old('quantity', 1) }}"
                                @if($service->pricing_model === 'fixed') readonly @endif
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('quantity') border-red-500 @enderror"
                            >
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if($service->pricing_model === 'fixed')
                                <p class="mt-1 text-sm text-gray-500">Fixed-price services have a quantity of 1.</p>
                            @endif
                        </div>

                        <!-- Deadline -->
                        <div class="mb-6">
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                                Deadline <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="deadline" 
                                name="deadline" 
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                max="{{ now()->addYear()->format('Y-m-d') }}"
                                value="{{ old('deadline', now()->addDays($service->delivery_days)->format('Y-m-d')) }}"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('deadline') border-red-500 @enderror"
                            >
                            @error('deadline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Suggested deadline based on delivery time: {{ now()->addDays($service->delivery_days)->format('M d, Y') }}</p>
                        </div>

                        <!-- Attachment Files -->
                        <div class="mb-6">
                            <label for="attachment_files" class="block text-sm font-medium text-gray-700 mb-2">
                                Attachment Files (Optional)
                            </label>
                            <input 
                                type="file" 
                                id="attachment_files" 
                                name="attachment_files[]" 
                                multiple
                                accept=".jpg,.jpeg,.png,.pdf,.docx"
                                class="w-full @error('attachment_files') border-red-500 @enderror"
                            >
                            @error('attachment_files')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Upload reference files, mockups, or any materials that will help the provider (max 5 files, 5MB each).</p>
                        </div>

                        <!-- Order Summary -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-3">Order Summary</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">${{ number_format($service->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Platform Fee (15%):</span>
                                    <span id="platform-fee">${{ number_format($service->price * 0.15, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                                    <span>Total:</span>
                                    <span id="total">${{ number_format($service->price * 1.15, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms Agreement -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" name="agree_terms" required class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-indigo-600 hover:underline">terms of service</a> and understand that payment will be held in escrow until I approve the deliverables.
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('client.services.show', $service) }}" class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update order summary when quantity changes
        const quantityInput = document.getElementById('quantity');
        const basePrice = {{ $service->price }};
        
        quantityInput?.addEventListener('input', function() {
            const quantity = parseInt(this.value) || 1;
            const subtotal = basePrice * quantity;
            const platformFee = subtotal * 0.15;
            const total = subtotal + platformFee;
            
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('platform-fee').textContent = '$' + platformFee.toFixed(2);
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        });
    </script>
    @endpush
</x-app-layout>
