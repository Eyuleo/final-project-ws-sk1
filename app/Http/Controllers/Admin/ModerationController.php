<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    /**
     * Display moderation dashboard
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'services');

        $data = match ($type) {
            'services' => $this->getPendingServices(),
            'reviews' => $this->getFlaggedReviews(),
            'users' => $this->getSuspiciousUsers(),
            default => $this->getPendingServices(),
        };

        return view('admin.moderation.index', compact('type', 'data'));
    }

    /**
     * Get services pending review
     */
    protected function getPendingServices()
    {
        return ServiceListing::with(['studentProfile.user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
    }

    /**
     * Get flagged reviews
     */
    protected function getFlaggedReviews()
    {
        return Review::with(['order.serviceListing', 'reviewer', 'reviewed'])
            ->where('is_visible', false)
            ->latest()
            ->paginate(20);
    }

    /**
     * Get suspicious users
     */
    protected function getSuspiciousUsers()
    {
        return User::where('is_active', false)
            ->whereIn('role', ['student', 'client'])
            ->with(['studentProfile', 'clientProfile'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Approve a service listing
     */
    public function approveService(ServiceListing $service)
    {
        $service->update([
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', 'Service listing approved.');
    }

    /**
     * Reject a service listing
     */
    public function rejectService(Request $request, ServiceListing $service)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->back()
            ->with('success', 'Service listing rejected.');
    }

    /**
     * Hide a review
     */
    public function hideReview(Review $review)
    {
        $review->update(['is_visible' => false]);

        return redirect()->back()
            ->with('success', 'Review hidden from public view.');
    }

    /**
     * Show a review
     */
    public function showReview(Review $review)
    {
        $review->update([
            'is_visible' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Review is now visible.');
    }

    /**
     * Suspend a user
     */
    public function suspendUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user->update([
            'is_active' => false,
            'suspension_reason' => $request->reason,
        ]);

        return redirect()->back()
            ->with('success', 'User suspended.');
    }

    /**
     * Activate a user
     */
    public function activateUser(User $user)
    {
        $user->update([
            'is_active' => true,
            'suspension_reason' => null,
        ]);

        return redirect()->back()
            ->with('success', 'User activated.');
    }
}
