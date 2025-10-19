<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use App\Models\Order;
use App\Services\FileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of message conversations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all orders where user is either student or client
        $orders = Order::where(function ($query) use ($user) {
            if ($user->role === 'student') {
                $query->where('student_profile_id', $user->studentProfile->id);
            } elseif ($user->role === 'client') {
                $query->where('client_profile_id', $user->clientProfile->id);
            }
        })
        ->whereHas('messages') // Only orders with messages
        ->with([
            'messages' => function ($query) {
                $query->latest()->limit(1); // Get last message
            },
            'serviceListing',
            'studentProfile.user',
            'clientProfile.user'
        ])
        ->get();

        // Calculate unread count for each conversation
        $conversations = $orders->map(function ($order) use ($user) {
            $unreadCount = $order->messages()
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'order' => $order,
                'last_message' => $order->messages->first(),
                'unread_count' => $unreadCount,
                'other_party' => $user->role === 'student' 
                    ? $order->clientProfile->user 
                    : $order->studentProfile->user,
            ];
        })->sortByDesc(function ($conversation) {
            return $conversation['last_message']->created_at ?? null;
        });

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display a specific conversation
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Debug: Check if user has profile
        \Log::info('Message show - User info', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'has_student_profile' => $user->studentProfile !== null,
            'has_client_profile' => $user->clientProfile !== null,
            'student_profile_id' => $user->studentProfile?->id,
            'client_profile_id' => $user->clientProfile?->id,
        ]);
        
        // Eager load relationships needed for authorization
        $order->load(['studentProfile', 'clientProfile']);
        
        \Log::info('Message show - Order info', [
            'order_id' => $order->id,
            'order_student_profile_id' => $order->student_profile_id,
            'order_client_profile_id' => $order->client_profile_id,
        ]);
        
        // Authorize using the MessagePolicy explicitly
        Gate::authorize('viewMessages', $order);

        // Load messages with sender information
        $messages = $order->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        $order->messages()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Get other party
        $otherParty = $user->role === 'student' 
            ? $order->clientProfile->user 
            : $order->studentProfile->user;

        return view('messages.show', compact('order', 'messages', 'otherParty'));
    }

    /**
     * Store a new message
     */
    public function store(MessageRequest $request)
    {
        $user = Auth::user();
        $order = Order::findOrFail($request->order_id);

        Gate::authorize('sendMessage', $order);

        // Determine receiver
        $receiverId = $user->role === 'student' 
            ? $order->clientProfile->user_id 
            : $order->studentProfile->user_id;

        // Handle attachment files
        $attachmentPaths = null;
        if ($request->hasFile('attachment_files')) {
            $attachmentPaths = [];
            foreach ($request->file('attachment_files') as $file) {
                $attachmentPaths[] = $this->fileUploadService->uploadMessageAttachment($file, $order->id);
            }
        }

        // Create message
        $message = DB::transaction(function () use ($order, $user, $receiverId, $request, $attachmentPaths) {
            $message = Message::create([
                'order_id' => $order->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'attachment_path' => $attachmentPaths,
                'is_read' => false,
            ]);

            return $message;
        });

        // Load sender relationship before broadcasting
        $message->load('sender');

        // Broadcast the message immediately (not queued)
        event(new MessageSent($message));

        // Queue notification job
        \App\Jobs\SendMessageNotification::dispatch($message);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('messages.show', $order)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        $this->authorize('view', $message);

        if ($message->receiver_id === Auth::id()) {
            $message->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
