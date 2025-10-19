<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $otherParty->name }}
                </h2>
                <p class="text-sm text-gray-600">
                    Order #{{ $order->order_number }} - {{ $order->serviceListing->title }}
                </p>
            </div>
            <a href="{{ route('messages.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                <!-- Order Info Banner -->
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($order->status === 'completed') bg-purple-100 text-purple-800
                                @elseif($order->status === 'approved') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <span class="text-sm text-gray-600">
                                Deadline: {{ $order->deadline->format('M d, Y') }}
                            </span>
                        </div>
                        <a href="{{ auth()->user()->role === 'student' ? route('student.orders.show', $order) : route('client.orders.show', $order) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800">
                            View Order Details →
                        </a>
                    </div>
                </div>

                <!-- Messages Thread -->
                <x-message-thread :messages="$messages" :order="$order" />

                <!-- Message Input Form -->
                <div class="p-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data" id="message-form">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        <div class="mb-3">
                            <textarea 
                                name="message" 
                                id="message-input"
                                rows="3" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type your message..."></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <label for="attachment_files" class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    Attach Files
                                </label>
                                <input 
                                    type="file" 
                                    id="attachment_files" 
                                    name="attachment_files[]" 
                                    multiple 
                                    class="hidden"
                                    accept="image/*,.pdf,.doc,.docx">
                                <span id="file-count" class="text-sm text-gray-500"></span>
                            </div>

                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Show file count when files are selected
        document.getElementById('attachment_files').addEventListener('change', function(e) {
            const fileCount = e.target.files.length;
            const fileCountEl = document.getElementById('file-count');
            if (fileCount > 0) {
                fileCountEl.textContent = `${fileCount} file${fileCount > 1 ? 's' : ''} selected`;
            } else {
                fileCountEl.textContent = '';
            }
        });

        // Auto-scroll to bottom of messages
        window.addEventListener('load', function() {
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
    @endpush
</x-app-layout>
