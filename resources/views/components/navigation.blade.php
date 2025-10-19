<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-blue-600" />
                    </a>
                    <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="ml-3 text-xl font-bold text-gray-900">
                        {{ config('app.name') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(Auth::user()->role === 'student')
                            <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('student.services.index')" :active="request()->routeIs('student.services.*')">
                                {{ __('My Services') }}
                            </x-nav-link>
                            <x-nav-link :href="route('student.orders.index')" :active="request()->routeIs('student.orders.*')">
                                {{ __('Orders') }}
                            </x-nav-link>
                            <x-nav-link :href="route('student.earnings.index')" :active="request()->routeIs('student.earnings.*')">
                                {{ __('Earnings') }}
                            </x-nav-link>
                            <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                                {{ __('Messages') }}
                            </x-nav-link>
                        @elseif(Auth::user()->role === 'client')
                            <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                                {{ __('Browse Services') }}
                            </x-nav-link>
                            <x-nav-link :href="route('client.orders.index')" :active="request()->routeIs('client.orders.*')">
                                {{ __('My Orders') }}
                            </x-nav-link>
                            <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                                {{ __('Messages') }}
                            </x-nav-link>
                        @elseif(Auth::user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                            {{ __('Browse Services') }}
                        </x-nav-link>
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            {{ __('Categories') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none mr-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Notification Badge -->
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center">
                                    @if(Auth::user()->profile_picture)
                                        <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover mr-2">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold mr-2">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span>{{ Auth::user()->name }}</span>
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->role === 'student')
                                <x-dropdown-link :href="route('student.profile.show')">
                                    {{ __('My Profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('student.profile.edit')">
                                    {{ __('Edit Profile') }}
                                </x-dropdown-link>
                            @elseif(Auth::user()->role === 'client')
                                <x-dropdown-link :href="route('client.profile.show')">
                                    {{ __('My Profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('client.profile.edit')">
                                    {{ __('Edit Profile') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 mr-4">Log in</a>
                    <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Get Started</a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
            <div class="pt-2 pb-3 space-y-1">
                @if(Auth::user()->role === 'student')
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.services.index')" :active="request()->routeIs('student.services.*')">
                        {{ __('My Services') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.orders.index')" :active="request()->routeIs('student.orders.*')">
                        {{ __('Orders') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.earnings.index')" :active="request()->routeIs('student.earnings.*')">
                        {{ __('Earnings') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                        {{ __('Messages') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->role === 'client')
                    <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                        {{ __('Browse Services') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.orders.index')" :active="request()->routeIs('client.orders.*')">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                        {{ __('Messages') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(Auth::user()->role === 'student')
                        <x-responsive-nav-link :href="route('student.profile.show')">
                            {{ __('My Profile') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('student.profile.edit')">
                            {{ __('Edit Profile') }}
                        </x-responsive-nav-link>
                    @elseif(Auth::user()->role === 'client')
                        <x-responsive-nav-link :href="route('client.profile.show')">
                            {{ __('My Profile') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('client.profile.edit')">
                            {{ __('Edit Profile') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                    {{ __('Browse Services') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ __('Categories') }}
                </x-responsive-nav-link>
            </div>
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                        Get Started
                    </a>
                </div>
            </div>
        @endauth
    </div>
</nav>
