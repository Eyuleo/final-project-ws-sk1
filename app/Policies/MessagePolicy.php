<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\Order;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine if the user can view messages for an order
     */
    public function viewMessages(User $user, Order $order): bool
    {
        // Debug logging
        \Log::info('MessagePolicy viewMessages check', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'order_id' => $order->id,
            'order_client_profile_id' => $order->client_profile_id,
            'order_student_profile_id' => $order->student_profile_id,
        ]);

        // User must be either the student or client for this order
        if ($user->role === 'student') {
            $userStudentProfileId = $user->studentProfile?->id;
            \Log::info('Student check', [
                'user_student_profile_id' => $userStudentProfileId,
                'order_student_profile_id' => $order->student_profile_id,
            ]);
            return $userStudentProfileId && $order->student_profile_id === $userStudentProfileId;
        }

        if ($user->role === 'client') {
            $userClientProfileId = $user->clientProfile?->id;
            \Log::info('Client check', [
                'user_client_profile_id' => $userClientProfileId,
                'order_client_profile_id' => $order->client_profile_id,
            ]);
            return $userClientProfileId && $order->client_profile_id === $userClientProfileId;
        }

        return false;
    }

    /**
     * Determine if the user can send a message for an order
     */
    public function sendMessage(User $user, Order $order): bool
    {
        // User must be either the student or client for this order
        if ($user->role === 'student') {
            return $order->studentProfile && $order->studentProfile->user_id === $user->id;
        }

        if ($user->role === 'client') {
            return $order->clientProfile && $order->clientProfile->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can view a specific message
     */
    public function view(User $user, Message $message): bool
    {
        // User must be either the sender or receiver
        return $message->sender_id === $user->id || $message->receiver_id === $user->id;
    }

    /**
     * Determine if the user can delete a message
     */
    public function delete(User $user, Message $message): bool
    {
        // Only the sender can delete their own messages
        return $message->sender_id === $user->id;
    }
}
