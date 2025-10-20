<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Service') }}
            </h2>
            <a href="{{ route('student.services.show', $service) }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Service
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('student.services.update', $service) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

                    <!-- Title -->
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Service Title')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $service->title)" required maxlength="255" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <x-input-label for="category" :value="__('Category')" />
                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category', $service->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Service Description')" />
                        <textarea id="description" name="description" rows="8" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required maxlength="5000">{{ old('description', $service->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Tags -->
                    <div>
                        <x-input-label for="tags" :value="__('Tags')" />
                        <x-text-input id="tags" class="block mt-1 w-full" type="text" name="tags" :value="old('tags', is_array($service->tags) ? implode(', ', $service->tags) : '')" placeholder="e.g., WordPress, React, Logo Design" />
                        <p class="mt-1 text-xs text-gray-500">Add relevant tags separated by commas (max 10 tags)</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Pricing & Delivery -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Delivery</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price (USD)')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="price" name="price" step="0.01" min="5" max="999999.99" value="{{ old('price', $service->price) }}" class="block w-full pl-7 pr-12 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" required>
                            </div>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Delivery Time -->
                        <div>
                            <x-input-label for="delivery_time" :value="__('Delivery Time (Days)')" />
                            <x-text-input id="delivery_time" class="block mt-1 w-full" type="number" name="delivery_time" :value="old('delivery_time', $service->delivery_days)" min="1" max="365" required />
                            <x-input-error :messages="$errors->get('delivery_time')" class="mt-2" />
                        </div>

                        <!-- Revisions -->
                        <div>
                            <x-input-label for="revisions" :value="__('Revisions Included')" />
                            <x-text-input id="revisions" class="block mt-1 w-full" type="number" name="revisions" :value="old('revisions', $service->revisions)" min="0" max="10" required />
                            <x-input-error :messages="$errors->get('revisions')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Requirements from Buyer</h3>

                    <div>
                        <x-input-label for="requirements" :value="__('What do you need from the buyer to get started?')" />
                        <textarea id="requirements" name="requirements" rows="5" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" maxlength="2000">{{ old('requirements', $service->requirements) }}</textarea>
                        <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Portfolio Samples -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Samples</h3>

                    <!-- Existing Samples -->
                    @if($service->portfolio_files && count($service->portfolio_files) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Samples</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($service->portfolio_files as $index => $sample)
                                    <div class="relative group">
                                        @if(str_starts_with($sample['type'], 'image/'))
                                            <img src="{{ Storage::url($sample['thumbnail'] ?? $sample['path']) }}" alt="{{ $sample['original_name'] }}" class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('student.services.delete-sample', [$service, $index]) }}" class="absolute top-2 right-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this sample?')" class="bg-red-600 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        @if(isset($sample['description']))
                                            <p class="mt-1 text-xs text-gray-600 truncate">{{ $sample['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Samples -->
                    <div>
                        <x-input-label for="portfolio_samples" :value="__('Add More Samples')" />
                        <input type="file" id="portfolio_samples" name="portfolio_samples[]" multiple accept="image/*,video/*,.pdf,.doc,.docx" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Upload additional samples. Max 5 files total, 50MB each.</p>
                        <x-input-error :messages="$errors->get('portfolio_samples')" class="mt-2" />
                    </div>

                    <!-- Sample Descriptions -->
                    <div class="mt-4" id="sample-descriptions" style="display: none;">
                        <x-input-label :value="__('Sample Descriptions (Optional)')" />
                        <div id="description-fields" class="space-y-2 mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pb-6">
                <a href="{{ route('student.services.show', $service) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
                <x-primary-button>
                    {{ __('Update Service') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('portfolio_samples').addEventListener('change', function(e) {
            const files = e.target.files;
            const descriptionsContainer = document.getElementById('sample-descriptions');
            const fieldsContainer = document.getElementById('description-fields');
            
            if (files.length > 0) {
                descriptionsContainer.style.display = 'block';
                fieldsContainer.innerHTML = '';
                
                for (let i = 0; i < files.length; i++) {
                    const field = document.createElement('input');
                    field.type = 'text';
                    field.name = `sample_descriptions[${i}]`;
                    field.placeholder = `Description for ${files[i].name}`;
                    field.className = 'block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm';
                    fieldsContainer.appendChild(field);
                }
            } else {
                descriptionsContainer.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-app-layout>
