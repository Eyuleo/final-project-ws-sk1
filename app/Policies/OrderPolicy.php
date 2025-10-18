<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        // Client or student involved in the order can view it
        return $order->clientProfile->user_id === $user->id
            || $order->studentProfile->user_id === $user->id
            || $user->role === 'admin';
    }

    /**
     * Determine if the user can accept the order
     */
    public function accept(User $user, Order $order): bool
    {
        // Only the student who owns the service can accept
        return $user->role === 'student'
            && $order->studentProfile->user_id === $user->id
            && $order->status === 'pending';
    }

    /**
     * Determine if the user can decline the order
     */
    public function decline(User $user, Order $order): bool
    {
        // Only the student who owns the service can decline
        return $user->role === 'student'
            && $order->studentProfile->user_id === $user->id
            && $order->status === 'pending';
    }

    /**
     * Determine if the user can approve the order
     */
    public function approve(User $user, Order $order): bool
    {
        // Only the client who placed the order can approve
        return $user->role === 'client'
            && $order->clientProfile->user_id === $user->id
            && $order->status === 'completed';
    }

    /**
     * Determine if the user can request revision
     */
    public function requestRevision(User $user, Order $order): bool
    {
        // Only the client who placed the order can request revision
        return $user->role === 'client'
            && $order->clientProfile->user_id === $user->id
            && $order->status === 'completed'
            && $order->revision_count < $order->max_revisions;
    }

    /**
     * Determine if the user can open a dispute
     */
    public function dispute(User $user, Order $order): bool
    {
        // Only the client can open a dispute after exhausting revisions
        return $user->role === 'client'
            && $order->clientProfile->user_id === $user->id
            && $order->status === 'completed'
            && $order->revision_count >= $order->max_revisions;
    }

    /**
     * Determine if the user can upload deliverables
     */
    public function uploadDeliverables(User $user, Order $order): bool
    {
        // Only the student can upload deliverables
        return $user->role === 'student'
            && $order->studentProfile->user_id === $user->id
            && in_array($order->status, ['accepted', 'in_progress', 'revision_requested']);
    }

    /**
     * Determine if the user can cancel the order
     */
    public function cancel(User $user, Order $order): bool
    {
        // Client or student can cancel before work starts, admin can always cancel
        if ($user->role === 'admin') {
            return true;
        }

        $isParticipant = $order->clientProfile->user_id === $user->id
            || $order->studentProfile->user_id === $user->id;

        return $isParticipant && in_array($order->status, ['pending', 'pending_payment']);
    }

    /**
     * Determine if the user can message about the order
     */
    public function message(User $user, Order $order): bool
    {
        // Client or student involved in the order can message
        return $order->clientProfile->user_id === $user->id
            || $order->studentProfile->user_id === $user->id;
    }
}
