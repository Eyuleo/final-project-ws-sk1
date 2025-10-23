<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Category') }}
            </h2>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Category Name -->
                        <div>
                            <x-input-label for="name" :value="__('Category Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required autofocus placeholder="e.g., Web Development" />
                            <p class="mt-1 text-xs text-gray-500">A unique name for the category (slug will be auto-generated if changed)</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" placeholder="Brief description of this category...">{{ old('description', $category->description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Optional description to help users understand this category</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Icon Selection -->
                        <div>
                            <x-input-label for="icon" :value="__('Icon')" />
                            <select id="icon" name="icon" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">Select an icon</option>
                                <option value="palette" {{ old('icon', $category->icon) === 'palette' ? 'selected' : '' }}>🎨 Palette (Design)</option>
                                <option value="code" {{ old('icon', $category->icon) === 'code' ? 'selected' : '' }}>💻 Code (Development)</option>
                                <option value="smartphone" {{ old('icon', $category->icon) === 'smartphone' ? 'selected' : '' }}>📱 Smartphone (Mobile)</option>
                                <option value="pen-tool" {{ old('icon', $category->icon) === 'pen-tool' ? 'selected' : '' }}>✍️ Pen Tool (Writing)</option>
                                <option value="languages" {{ old('icon', $category->icon) === 'languages' ? 'selected' : '' }}>🌐 Languages (Translation)</option>
                                <option value="video" {{ old('icon', $category->icon) === 'video' ? 'selected' : '' }}>🎥 Video (Media)</option>
                                <option value="trending-up" {{ old('icon', $category->icon) === 'trending-up' ? 'selected' : '' }}>📈 Trending Up (Marketing)</option>
                                <option value="database" {{ old('icon', $category->icon) === 'database' ? 'selected' : '' }}>💾 Database (Data)</option>
                                <option value="book-open" {{ old('icon', $category->icon) === 'book-open' ? 'selected' : '' }}>📖 Book (Education)</option>
                                <option value="layout" {{ old('icon', $category->icon) === 'layout' ? 'selected' : '' }}>🖼️ Layout (UI/UX)</option>
                                <option value="camera" {{ old('icon', $category->icon) === 'camera' ? 'selected' : '' }}>📷 Camera (Photography)</option>
                                <option value="user-check" {{ old('icon', $category->icon) === 'user-check' ? 'selected' : '' }}>✅ User Check (Assistant)</option>
                                <option value="music" {{ old('icon', $category->icon) === 'music' ? 'selected' : '' }}>🎵 Music (Audio)</option>
                                <option value="briefcase" {{ old('icon', $category->icon) === 'briefcase' ? 'selected' : '' }}>💼 Briefcase (Business)</option>
                                <option value="globe" {{ old('icon', $category->icon) === 'globe' ? 'selected' : '' }}>🌍 Globe (General)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Choose an icon that represents this category</p>
                            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <x-input-label for="sort_order" :value="__('Sort Order')" />
                            <x-text-input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order" :value="old('sort_order', $category->sort_order)" min="0" placeholder="0" />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first (e.g., 1, 2, 3...)</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">Active (visible to users)</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Inactive categories won't appear in category listings</p>
                        </div>

                        <!-- Category Info -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Category Information</h4>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-gray-500">Slug</dt>
                                    <dd class="text-gray-900 font-mono">{{ $category->slug }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Service Listings</dt>
                                    <dd class="text-gray-900">{{ $category->serviceListings()->count() }} services</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Created</dt>
                                    <dd class="text-gray-900">{{ $category->created_at->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Last Updated</dt>
                                    <dd class="text-gray-900">{{ $category->updated_at->format('M d, Y') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Category') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
