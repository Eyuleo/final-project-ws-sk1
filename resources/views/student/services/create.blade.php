<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Service') }}
            </h2>
            <a href="{{ route('student.services.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Services
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('student.services.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

                    <!-- Title -->
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Service Title')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus maxlength="255" placeholder="e.g., I will create a professional website for your business" />
                        <p class="mt-1 text-xs text-gray-500">Create a clear, descriptive title that explains what you offer</p>
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <x-input-label for="category" :value="__('Category')" />
                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Service Description')" />
                        <textarea id="description" name="description" rows="8" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required maxlength="5000" placeholder="Describe your service in detail. What will you deliver? What makes your service unique? What experience do you have?">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Provide a detailed description of your service (max 5000 characters)</p>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Tags -->
                    <div>
                        <x-input-label for="tags" :value="__('Tags')" />
                        <x-text-input id="tags" class="block mt-1 w-full" type="text" name="tags" :value="old('tags')" placeholder="e.g., WordPress, React, Logo Design, SEO" />
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
                                <input type="number" id="price" name="price" step="0.01" min="5" max="999999.99" value="{{ old('price') }}" class="block w-full pl-7 pr-12 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md" placeholder="0.00" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimum $5</p>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Delivery Time -->
                        <div>
                            <x-input-label for="delivery_time" :value="__('Delivery Time (Days)')" />
                            <x-text-input id="delivery_time" class="block mt-1 w-full" type="number" name="delivery_time" :value="old('delivery_time')" min="1" max="365" required placeholder="7" />
                            <p class="mt-1 text-xs text-gray-500">1-365 days</p>
                            <x-input-error :messages="$errors->get('delivery_time')" class="mt-2" />
                        </div>

                        <!-- Revisions -->
                        <div>
                            <x-input-label for="revisions" :value="__('Revisions Included')" />
                            <x-text-input id="revisions" class="block mt-1 w-full" type="number" name="revisions" :value="old('revisions', 2)" min="0" max="10" required />
                            <p class="mt-1 text-xs text-gray-500">0-10 revisions</p>
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
                        <textarea id="requirements" name="requirements" rows="5" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" maxlength="2000" placeholder="e.g., Brand colors, logo files, website content, reference websites, etc.">{{ old('requirements') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">List any information or files you need from buyers (optional, max 2000 characters)</p>
                        <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Portfolio Samples -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Samples</h3>

                    <div>
                        <x-input-label for="portfolio_samples" :value="__('Upload Sample Work')" />
                        <input type="file" id="portfolio_samples" name="portfolio_samples[]" multiple accept="image/*,video/*,.pdf,.doc,.docx" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Upload images, videos, or documents showcasing your work. Max 5 files, 50MB each.</p>
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
                <a href="{{ route('student.services.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
                <x-primary-button>
                    {{ __('Create Service') }}
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
