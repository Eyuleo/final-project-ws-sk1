<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Profile') }}
            </h2>
            <a href="{{ route('student.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                    <!-- Profile Picture -->
                    <div class="flex-shrink-0">
                        @if($profile->profile_picture)
                            <img src="{{ Storage::url($profile->profile_picture) }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full object-cover border-4 border-gray-200">
                        @else
                            <div class="h-32 w-32 rounded-full bg-blue-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Profile Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            @if($profile->available_for_work)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Available for Work
                                </span>
                            @endif
                        </div>

                        @if($profile->tagline)
                            <p class="text-lg text-gray-600 mb-3">{{ $profile->tagline }}</p>
                        @endif

                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ $profile->university }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ $profile->field_of_study }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Year {{ $profile->year_of_study }}
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <x-rating-stars :rating="$profile->average_rating ?? 0" size="sm" :showNumber="true" />
                                <span class="ml-2 text-sm text-gray-500">({{ $profile->total_reviews ?? 0 }} reviews)</span>
                            </div>
                            @if($profile->hourly_rate)
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold">${{ number_format($profile->hourly_rate, 2) }}</span>/hour
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex md:flex-col space-x-6 md:space-x-0 md:space-y-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['completed_orders'] }}</div>
                            <div class="text-xs text-gray-500">Completed</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['active_services'] }}</div>
                            <div class="text-xs text-gray-500">Services</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_earnings'], 0) }}</div>
                            <div class="text-xs text-gray-500">Earned</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About -->
                @if($profile->bio)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">About Me</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $profile->bio }}</p>
                        </div>
                    </div>
                @endif

                <!-- Skills -->
                @if($profile->skills && count($profile->skills) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profile->skills as $skill)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Portfolio -->
                @if($profile->portfolio_files && count($profile->portfolio_files) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Portfolio</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($profile->portfolio_files as $file)
                                    <div class="relative group">
                                        @if(str_starts_with($file['type'], 'image/'))
                                            <img src="{{ Storage::url($file['path']) }}" alt="{{ $file['original_name'] }}" class="w-full h-40 object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if(isset($file['description']))
                                            <p class="mt-2 text-xs text-gray-600">{{ $file['description'] }}</p>
                                        @endif
                                        <a href="{{ Storage::url($file['path']) }}" target="_blank" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-lg transition flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- My Services -->
                @if($profile->serviceListings()->where('status', 'active')->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">My Services</h3>
                                <a href="{{ route('student.services.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($profile->serviceListings()->where('status', 'active')->latest()->take(6)->get() as $service)
                                    <x-service-card :service="$service" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                @if(isset($reviews) && $reviews->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Reviews ({{ $profile->total_reviews ?? 0 }})</h3>
                                <div class="flex items-center">
                                    <x-rating-stars :rating="$profile->average_rating ?? 0" size="sm" />
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ number_format($profile->average_rating ?? 0, 1) }}</span>
                                </div>
                            </div>

                            <!-- Rating Breakdown -->
                            @if(isset($ratingBreakdown))
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Rating Breakdown</h4>
                                    <div class="space-y-2">
                                        @foreach($ratingBreakdown as $rating => $count)
                                            <div class="flex items-center">
                                                <span class="text-sm text-gray-600 w-12">{{ $rating }} star</span>
                                                <div class="flex-1 mx-3">
                                                    <div class="bg-gray-200 rounded-full h-2">
                                                        @php
                                                            $total = array_sum($ratingBreakdown);
                                                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                                        @endphp
                                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500 w-8">{{ $count }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Common Tags -->
                            @if(isset($commonTags) && count($commonTags) > 0)
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Most Common Tags</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($commonTags as $tag => $count)
                                            @php
                                                $tagLabels = [
                                                    'professional' => 'Professional',
                                                    'responsive' => 'Responsive',
                                                    'quality' => 'High Quality',
                                                    'communication' => 'Great Communication',
                                                    'timely' => 'On Time',
                                                    'creative' => 'Creative',
                                                    'exceeded_expectations' => 'Exceeded Expectations'
                                                ];
                                                $label = $tagLabels[$tag] ?? ucfirst(str_replace('_', ' ', $tag));
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                {{ $label }} ({{ $count }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Review List -->
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <x-review-card :review="$review" />
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($reviews->hasPages())
                                <div class="mt-6">
                                    {{ $reviews->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif(isset($reviews))
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reviews</h3>
                            <p class="text-gray-500 text-center py-8">No reviews yet. Complete orders to start receiving reviews!</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Languages -->
                @if($profile->languages && count($profile->languages) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Languages</h3>
                            <ul class="space-y-2">
                                @foreach($profile->languages as $language)
                                    <li class="flex items-center text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $language }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Social Links -->
                @if($profile->github_url || $profile->linkedin_url || $profile->portfolio_url || $profile->behance_url)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Links</h3>
                            <div class="space-y-3">
                                @if($profile->github_url)
                                    <a href="{{ $profile->github_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                        </svg>
                                        GitHub
                                    </a>
                                @endif
                                @if($profile->linkedin_url)
                                    <a href="{{ $profile->linkedin_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                        LinkedIn
                                    </a>
                                @endif
                                @if($profile->portfolio_url)
                                    <a href="{{ $profile->portfolio_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        Portfolio Website
                                    </a>
                                @endif
                                @if($profile->behance_url)
                                    <a href="{{ $profile->behance_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M22 7h-7v-2h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14h-8.027c.13 3.211 3.483 3.312 4.588 2.029h3.168zm-7.686-4h4.965c-.105-1.547-1.136-2.219-2.477-2.219-1.466 0-2.277.768-2.488 2.219zm-9.574 6.988h-6.466v-14.967h6.953c5.476.081 5.58 5.444 2.72 6.906 3.461 1.26 3.577 8.061-3.207 8.061zm-3.466-8.988h3.584c2.508 0 2.906-3-.312-3h-3.272v3zm3.391 3h-3.391v3.016h3.341c3.055 0 2.868-3.016.05-3.016z"/>
                                        </svg>
                                        Behance
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Education -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Education</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $profile->field_of_study }}</p>
                                <p class="text-sm text-gray-600">{{ $profile->university }}</p>
                                <p class="text-xs text-gray-500">Year {{ $profile->year_of_study }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
