<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Profile') }}
            </h2>
            <a href="{{ route('student.profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Profile
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Account Settings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus placeholder="Your full name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required placeholder="your.email@example.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Change Password</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Current Password -->
                            <div>
                                <x-input-label for="current_password" :value="__('Current Password')" />
                                <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" autocomplete="current-password" placeholder="Enter current password" />
                                <p class="mt-1 text-xs text-gray-500">Required to change password</p>
                                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                            </div>

                            <!-- New Password -->
                            <div>
                                <x-input-label for="password" :value="__('New Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" placeholder="Enter new password" />
                                <p class="mt-1 text-xs text-gray-500">Min 8 characters</p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                        <!-- Password Confirmation -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Confirm new password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

                    <!-- Profile Picture -->
                    <div class="mb-6">
                        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                        <div class="mt-2 flex items-center space-x-6">
                            @if($profile->profile_picture)
                                <img src="{{ Storage::url($profile->profile_picture) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                            @else
                                <div class="h-24 w-24 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF or WebP. Max 5MB.</p>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                    </div>

                    <!-- Tagline -->
                    <div class="mb-4">
                        <x-input-label for="tagline" :value="__('Professional Tagline')" />
                        <x-text-input id="tagline" class="block mt-1 w-full" type="text" name="tagline" :value="old('tagline', $profile->tagline)" maxlength="100" placeholder="e.g., Full-Stack Developer | UI/UX Designer" />
                        <p class="mt-1 text-xs text-gray-500">A brief headline that describes what you do (max 100 characters)</p>
                        <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <x-input-label for="bio" :value="__('About Me')" />
                        <textarea id="bio" name="bio" rows="5" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" maxlength="1000" placeholder="Tell clients about yourself, your experience, and what makes you unique...">{{ old('bio', $profile->bio) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Education</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- University -->
                        <div>
                            <x-input-label for="university" :value="__('University')" />
                            <select id="university" name="university" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">Select your university</option>
                                @foreach(['Addis Ababa University', 'Bahir Dar University', 'Jimma University', 'Hawassa University', 'Mekelle University', 'Gondar University', 'Haramaya University', 'Arba Minch University', 'Other'] as $uni)
                                    <option value="{{ $uni }}" {{ old('university', $profile->university) == $uni ? 'selected' : '' }}>{{ $uni }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('university')" class="mt-2" />
                        </div>

                        <!-- Year of Study -->
                        <div>
                            <x-input-label for="year_of_study" :value="__('Year of Study')" />
                            <select id="year_of_study" name="year_of_study" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">Select year</option>
                                @foreach(['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year', '5' => '5th Year', 'graduate' => 'Graduate Student'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('year_of_study', $profile->year_of_study) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('year_of_study')" class="mt-2" />
                        </div>

                        <!-- Field of Study -->
                        <div class="md:col-span-2">
                            <x-input-label for="field_of_study" :value="__('Field of Study')" />
                            <x-text-input id="field_of_study" class="block mt-1 w-full" type="text" name="field_of_study" :value="old('field_of_study', $profile->field_of_study)" required placeholder="e.g., Computer Science, Graphic Design" />
                            <x-input-error :messages="$errors->get('field_of_study')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills & Languages -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills & Languages</h3>

                    <!-- Skills -->
                    <div class="mb-4">
                        <x-input-label for="skills" :value="__('Skills')" />
                        <input type="text" id="skills" name="skills" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" value="{{ old('skills', is_array($profile->skills) ? implode(', ', $profile->skills) : '') }}" placeholder="e.g., JavaScript, Python, Graphic Design, Video Editing">
                        <p class="mt-1 text-xs text-gray-500">Separate skills with commas. Maximum 20 skills.</p>
                        <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                    </div>

                    <!-- Languages -->
                    <div>
                        <x-input-label for="languages" :value="__('Languages')" />
                        <input type="text" id="languages" name="languages" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" value="{{ old('languages', is_array($profile->languages) ? implode(', ', $profile->languages) : '') }}" placeholder="e.g., English, Amharic, French">
                        <p class="mt-1 text-xs text-gray-500">Separate languages with commas. Maximum 10 languages.</p>
                        <x-input-error :messages="$errors->get('languages')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Portfolio -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio</h3>

                    <!-- Existing Portfolio Files -->
                    @if($profile->portfolio_files && count($profile->portfolio_files) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Portfolio Files</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($profile->portfolio_files as $index => $file)
                                    <div class="relative group">
                                        @if(str_starts_with($file['type'], 'image/'))
                                            <img src="{{ Storage::url($file['path']) }}" alt="{{ $file['original_name'] }}" class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <button type="button" onclick="deletePortfolioFile({{ $index }})" class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        @if(isset($file['description']))
                                            <p class="mt-1 text-xs text-gray-600 truncate">{{ $file['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Portfolio Files -->
                    <div class="mb-4">
                        <x-input-label for="portfolio_files" :value="__('Add Portfolio Files')" />
                        <input type="file" id="portfolio_files" name="portfolio_files[]" multiple accept="image/*,video/*,.pdf,.doc,.docx" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Images, videos, PDFs, or documents. Max 50MB per file. Maximum 10 files total.</p>
                        <x-input-error :messages="$errors->get('portfolio_files')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Links</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- GitHub -->
                        <div>
                            <x-input-label for="github_url" :value="__('GitHub URL')" />
                            <x-text-input id="github_url" class="block mt-1 w-full" type="url" name="github_url" :value="old('github_url', $profile->github_url)" placeholder="https://github.com/username" />
                            <x-input-error :messages="$errors->get('github_url')" class="mt-2" />
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <x-input-label for="linkedin_url" :value="__('LinkedIn URL')" />
                            <x-text-input id="linkedin_url" class="block mt-1 w-full" type="url" name="linkedin_url" :value="old('linkedin_url', $profile->linkedin_url)" placeholder="https://linkedin.com/in/username" />
                            <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                        </div>

                        <!-- Portfolio Website -->
                        <div>
                            <x-input-label for="portfolio_url" :value="__('Portfolio Website')" />
                            <x-text-input id="portfolio_url" class="block mt-1 w-full" type="url" name="portfolio_url" :value="old('portfolio_url', $profile->portfolio_url)" placeholder="https://yourportfolio.com" />
                            <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                        </div>

                        <!-- Behance -->
                        <div>
                            <x-input-label for="behance_url" :value="__('Behance URL')" />
                            <x-text-input id="behance_url" class="block mt-1 w-full" type="url" name="behance_url" :value="old('behance_url', $profile->behance_url)" placeholder="https://behance.net/username" />
                            <x-input-error :messages="$errors->get('behance_url')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Availability & Rates -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Availability & Rates</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Available for Work -->
                        <div>
                            <label for="available_for_work" class="inline-flex items-center">
                                <input id="available_for_work" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="available_for_work" value="1" {{ old('available_for_work', $profile->available_for_work) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">I am currently available for work</span>
                            </label>
                        </div>

                        <!-- Hourly Rate -->
                        <div>
                            <x-input-label for="hourly_rate" :value="__('Hourly Rate (USD)')" />
                            <x-text-input id="hourly_rate" class="block mt-1 w-full" type="number" step="0.01" min="0" name="hourly_rate" :value="old('hourly_rate', $profile->hourly_rate)" placeholder="25.00" />
                            <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pb-6">
                <a href="{{ route('student.profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
                <x-primary-button>
                    {{ __('Save Profile') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Hidden forms for portfolio file deletion (outside main form to avoid nesting) -->
        @if($profile->portfolio_files && count($profile->portfolio_files) > 0)
            @foreach($profile->portfolio_files as $index => $file)
                <form id="delete-portfolio-form-{{ $index }}" method="POST" action="{{ route('student.profile.portfolio.delete', $index) }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>

    @push('scripts')
    <script>
        function deletePortfolioFile(index) {
            if (confirm('Are you sure you want to delete this portfolio file?')) {
                document.getElementById('delete-portfolio-form-' + index).submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
