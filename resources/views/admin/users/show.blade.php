<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Profile -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">User Information</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center space-x-4 mb-6">
                            @if($user->profile_picture)
                                <img class="h-20 w-20 rounded-full" src="{{ Storage::url($user->profile_picture) }}" alt="">
                            @else
                                <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-xl">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <div class="mt-1 flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                        {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $user->role }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Joined</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y') }}</dd>
                            </div>
                            @if($user->role === 'student' && $user->studentProfile)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Bio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->studentProfile->bio ?? 'No bio provided' }}</dd>
                                </div>
                                @if($user->studentProfile->skills)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Skills</dt>
                                        <dd class="mt-1">
                                            @foreach($user->studentProfile->skills as $skill)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
                                                    {{ $skill }}
                                                </span>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Statistics -->
                @if($user->role === 'student')
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Student Statistics</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-2 gap-5">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Services</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_services'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Active Services</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['active_services'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Completed Orders</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['completed_orders'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Earnings</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($stats['total_earnings'], 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Average Rating</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['average_rating'], 1) }} ★</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @else
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Client Statistics</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-2 gap-5">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Completed Orders</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['completed_orders'] }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Total Spent</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Actions</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-3">
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                            </button>
                        </form>
                    </div>
                </div>

                @if($user->suspension_reason)
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Suspension Reason</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>{{ $user->suspension_reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
