@props(['messages', 'order'])

<div id="messages-container" class="p-6 space-y-4 max-h-96 overflow-y-auto">
    @if($messages->isEmpty())
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No messages yet. Start the conversation!</p>
        </div>
    @else
        @foreach($messages as $message)
            @php
                $isSender = $message->sender_id === auth()->id();
            @endphp
            
            <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md">
                    <!-- Message Bubble -->
                    <div class="rounded-lg px-4 py-3 {{ $isSender ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                        <p class="text-sm whitespace-pre-line break-words">{{ $message->message }}</p>
                        
                        <!-- Attachments -->
                        @if($message->attachment_path)
                            <div class="mt-2 space-y-1">
                                @foreach($message->attachment_path as $attachment)
                                    <a href="{{ Storage::url($attachment['path'] ?? $attachment) }}" 
                                       target="_blank"
                                       class="flex items-center text-xs {{ $isSender ? 'text-indigo-100 hover:text-white' : 'text-indigo-600 hover:text-indigo-800' }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        {{ basename($attachment['path'] ?? $attachment) }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message Info -->
                    <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500 {{ $isSender ? 'justify-end' : 'justify-start' }}">
                        @if(!$isSender)
                            <span class="font-medium">{{ $message->sender->name }}</span>
                            <span>•</span>
                        @endif
                        <span>{{ $message->created_at->format('M d, g:i A') }}</span>
                        @if($isSender && $message->is_read)
                            <span>•</span>
                            <span class="text-indigo-600">Read</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
