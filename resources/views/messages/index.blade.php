<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($conversations->isEmpty())
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No messages yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start a conversation by messaging about an order.</p>
                        <div class="mt-6">
                            <a href="{{ auth()->user()->role === 'student' ? route('student.orders.index') : route('client.orders.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Orders
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Conversations List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="divide-y divide-gray-200">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('messages.show', $conversation['order']) }}" 
                               class="block hover:bg-gray-50 transition-colors">
                                <div class="p-6">
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-lg">
                                                    {{ substr($conversation['other_party']->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Conversation Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $conversation['other_party']->name }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $conversation['last_message']->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 mb-2">
                                                Order #{{ $conversation['order']->order_number }} - {{ $conversation['order']->serviceListing->title }}
                                            </p>

                                            <div class="flex items-center justify-between">
                                                <p class="text-sm text-gray-500 truncate">
                                                    @if($conversation['last_message']->sender_id === auth()->id())
                                                        <span class="font-medium">You:</span>
                                                    @endif
                                                    {{ Str::limit($conversation['last_message']->message, 60) }}
                                                </p>

                                                @if($conversation['unread_count'] > 0)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $conversation['unread_count'] }} new
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Subscribe to all conversations for real-time updates
        if (window.Echo) {
            @foreach($conversations as $conversation)
                window.Echo.private('order.{{ $conversation["order"]->id }}')
                    .listen('MessageSent', (e) => {
                        console.log('New message on index:', e);
                        
                        // Find the conversation element
                        const conversationLink = document.querySelector('a[href="{{ route('messages.show', $conversation['order']) }}"]');
                        if (!conversationLink) return;

                        // Update last message
                        const lastMessageEl = conversationLink.querySelector('.text-sm.text-gray-500.truncate');
                        if (lastMessageEl) {
                            const prefix = e.message.sender_id === {{ auth()->id() }} ? '<span class="font-medium">You:</span> ' : '';
                            lastMessageEl.innerHTML = prefix + e.message.message.substring(0, 60) + (e.message.message.length > 60 ? '...' : '');
                        }

                        // Update timestamp
                        const timeEl = conversationLink.querySelector('.text-xs.text-gray-500');
                        if (timeEl) {
                            timeEl.textContent = 'Just now';
                        }

                        // Update or add unread badge if message is not from current user
                        if (e.message.sender_id !== {{ auth()->id() }}) {
                            const unreadBadge = conversationLink.querySelector('.bg-indigo-100');
                            if (unreadBadge) {
                                const count = parseInt(unreadBadge.textContent) || 0;
                                unreadBadge.textContent = (count + 1) + ' new';
                            } else {
                                const badgeContainer = conversationLink.querySelector('.flex.items-center.justify-between');
                                if (badgeContainer) {
                                    const badge = document.createElement('span');
                                    badge.className = 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800';
                                    badge.textContent = '1 new';
                                    badgeContainer.appendChild(badge);
                                }
                            }
                        }

                        // Move conversation to top
                        const parent = conversationLink.parentElement;
                        if (parent && parent.firstChild !== conversationLink) {
                            parent.insertBefore(conversationLink, parent.firstChild);
                        }
                    });
            @endforeach
        }
    </script>
    @endpush
</x-app-layout>
