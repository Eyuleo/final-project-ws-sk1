<x-app-layout>
    <x-slot name="title">{{ $user->name }} - Student Provider</x-slot>
    <x-slot name="description">{{ $profile->tagline ?? 'View ' . $user->name . '\'s profile and services' }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-8">
                    <!-- Profile Picture -->
                    <div class="flex-shrink-0">
                        @if($profile->profile_picture)
                            <img src="{{ Storage::url($profile->profile_picture) }}" alt="{{ $user->name }}" class="h-40 w-40 rounded-full object-cover border-4 border-blue-100 shadow-lg">
                        @else
                            <div class="h-40 w-40 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-5xl font-bold border-4 border-blue-100 shadow-lg">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Profile Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-4xl font-bold text-gray-900">{{ $user->name }}</h1>
                            @if($profile->available_for_work)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                    Available
                                </span>
                            @endif
                        </div>

                        @if($profile->tagline)
                            <p class="text-xl text-gray-600 mb-4">{{ $profile->tagline }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ $profile->university }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ $profile->field_of_study }}
                            </div>
                            @if($profile->hourly_rate)
                                <div class="flex items-center font-semibold text-blue-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${{ number_format($profile->hourly_rate, 2) }}/hour
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <x-rating-stars :rating="$profile->average_rating ?? 0" size="md" :showNumber="true" />
                                <span class="ml-2 text-sm text-gray-500">({{ $profile->total_reviews ?? 0 }} reviews)</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ $stats['completed_orders'] }}</span> orders completed
                            </div>
                        </div>
                    </div>

                    <!-- Note: Messaging is available after placing an order -->
                    @auth
                        @if(Auth::user()->role === 'client')
                            <div class="text-center text-sm text-gray-600 mt-4">
                                <p>Browse this provider's services below and place an order to start a conversation.</p>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About -->
                @if($profile->bio)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">About {{ explode(' ', $user->name)[0] }}</h2>
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $profile->bio }}</p>
                        </div>
                    </div>
                @endif

                <!-- Skills -->
                @if($profile->skills && count($profile->skills) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Skills</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profile->skills as $skill)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
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
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Portfolio</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($profile->portfolio_files as $file)
                                    <div class="relative group cursor-pointer">
                                        @if(str_starts_with($file['type'], 'image/'))
                                            <img src="{{ Storage::url($file['path']) }}" alt="{{ $file['original_name'] }}" class="w-full h-48 object-cover rounded-lg shadow-md group-hover:shadow-xl transition">
                                        @else
                                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-xl transition">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if(isset($file['description']))
                                            <p class="mt-2 text-sm text-gray-600">{{ $file['description'] }}</p>
                                        @endif
                                        <a href="{{ Storage::url($file['path']) }}" target="_blank" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 rounded-lg transition flex items-center justify-center">
                                            <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Services -->
                @if($services->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Services Offered</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($services as $service)
                                    <x-service-card :service="$service" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                @if($reviews->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Reviews</h2>
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <div class="border-b border-gray-200 pb-4 last:border-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $review->client->name }}</p>
                                                <x-rating-stars :rating="$review->rating" size="sm" />
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Orders Completed</span>
                                <span class="font-bold text-gray-900">{{ $stats['completed_orders'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Active Services</span>
                                <span class="font-bold text-gray-900">{{ $stats['active_services'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Response Time</span>
                                <span class="font-bold text-gray-900">~1 hour</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Languages -->
                @if($profile->languages && count($profile->languages) > 0)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Languages</h3>
                            <ul class="space-y-2">
                                @foreach($profile->languages as $language)
                                    <li class="flex items-center text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Connect</h3>
                            <div class="space-y-3">
                                @if($profile->github_url)
                                    <a href="{{ $profile->github_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                                        GitHub
                                    </a>
                                @endif
                                @if($profile->linkedin_url)
                                    <a href="{{ $profile->linkedin_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                        LinkedIn
                                    </a>
                                @endif
                                @if($profile->portfolio_url)
                                    <a href="{{ $profile->portfolio_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                        Portfolio
                                    </a>
                                @endif
                                @if($profile->behance_url)
                                    <a href="{{ $profile->behance_url }}" target="_blank" class="flex items-center text-gray-700 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M22 7h-7v-2h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14h-8.027c.13 3.211 3.483 3.312 4.588 2.029h3.168zm-7.686-4h4.965c-.105-1.547-1.136-2.219-2.477-2.219-1.466 0-2.277.768-2.488 2.219zm-9.574 6.988h-6.466v-14.967h6.953c5.476.081 5.58 5.444 2.72 6.906 3.461 1.26 3.577 8.061-3.207 8.061zm-3.466-8.988h3.584c2.508 0 2.906-3-.312-3h-3.272v3zm3.391 3h-3.391v3.016h3.341c3.055 0 2.868-3.016.05-3.016z"/></svg>
                                        Behance
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
