<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, Review $review): bool
    {
        // Anyone can view visible reviews
        if ($review->is_visible) {
            return true;
        }

        // Only admin can view hidden reviews
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create a review for an order.
     */
    public function review(User $user, Order $order): bool
    {
        // User must be a client
        if ($user->role !== 'client') {
            return false;
        }

        // Order must belong to the client
        if ($order->clientProfile->user_id !== $user->id) {
            return false;
        }

        // Order must be approved
        if ($order->status !== 'approved') {
            return false;
        }

        // Order must not already have a review
        if ($order->review()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, Review $review): bool
    {
        // Only the reviewer can update within 24 hours of creation
        if ($review->reviewer_id === $user->id) {
            return $review->created_at->diffInHours(now()) < 24;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        // Only the reviewer can delete within 1 hour of creation
        if ($review->reviewer_id === $user->id) {
            return $review->created_at->diffInMinutes(now()) < 60;
        }

        // Admin can delete anytime
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can hide/show the review (moderation).
     */
    public function moderate(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the review.
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the review.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }
}
