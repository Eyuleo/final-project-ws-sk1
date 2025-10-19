import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Real-time message updates
 * Listen for new messages on the order channel
 */
if (window.Echo && document.getElementById('messages-container')) {
    const orderId = document.querySelector('input[name="order_id"]')?.value;
    const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    
    console.log('Setting up real-time listener for order:', orderId);
    
    if (orderId) {
        window.Echo.private(`order.${orderId}`)
            .listen('MessageSent', (e) => {
                console.log('New message received:', e);
                
                // Don't add message if it's from the current user (already displayed)
                if (e.message.sender_id == currentUserId) {
                    return;
                }
                
                // Add the new message to the conversation
                addMessageToThread(e.message);
                
                // Scroll to bottom
                scrollToBottom();
                
                // Play notification sound (optional)
                playNotificationSound();
            });
    }
}

/**
 * Add a new message to the thread
 */
function addMessageToThread(messageData) {
    const messagesContainer = document.getElementById('messages-container');
    if (!messagesContainer) return;
    
    const senderName = messageData.sender_name || messageData.sender?.name || 'Unknown';
    
    const messageHtml = `
        <div class="flex justify-start">
            <div class="max-w-xs lg:max-w-md">
                <div class="rounded-lg px-4 py-3 bg-gray-100 text-gray-900">
                    <p class="text-sm whitespace-pre-line break-words">${escapeHtml(messageData.message)}</p>
                    ${messageData.attachment_path ? renderAttachments(messageData.attachment_path) : ''}
                </div>
                <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500 justify-start">
                    <span class="font-medium">${escapeHtml(senderName)}</span>
                    <span>•</span>
                    <span>${formatTime(messageData.created_at)}</span>
                </div>
            </div>
        </div>
    `;
    
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
}

/**
 * Render attachment links
 */
function renderAttachments(attachments) {
    if (!Array.isArray(attachments)) return '';
    
    return `
        <div class="mt-2 space-y-1">
            ${attachments.map(attachment => `
                <a href="/storage/${attachment.path || attachment}" 
                   target="_blank"
                   class="flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    ${basename(attachment.path || attachment)}
                </a>
            `).join('')}
        </div>
    `;
}

/**
 * Scroll messages container to bottom
 */
function scrollToBottom() {
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

/**
 * Play notification sound
 */
function playNotificationSound() {
    // Optional: Add a subtle notification sound
    // const audio = new Audio('/sounds/notification.mp3');
    // audio.play().catch(() => {});
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format timestamp
 */
function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

/**
 * Get basename from path
 */
function basename(path) {
    return path.split('/').pop();
}
