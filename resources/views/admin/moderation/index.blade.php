<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Content Moderation') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Type Tabs -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <a href="{{ route('admin.moderation.index', ['type' => 'services']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $type === 'services' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Services
                    </a>
                    <a href="{{ route('admin.moderation.index', ['type' => 'reviews']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $type === 'reviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Reviews
                    </a>
                    <a href="{{ route('admin.moderation.index', ['type' => 'users']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $type === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Users
                    </a>
                </nav>
            </div>
        </div>

        <!-- Content List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if($data->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($data as $item)
                        <li class="px-6 py-4 hover:bg-gray-50">
                            @if($type === 'services')
                                @include('admin.moderation.partials.service-item', ['service' => $item])
                            @elseif($type === 'reviews')
                                @include('admin.moderation.partials.review-item', ['review' => $item])
                            @else
                                @include('admin.moderation.partials.user-item', ['user' => $item])
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $data->links('pagination::tailwind') }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-500">No items requiring moderation</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
