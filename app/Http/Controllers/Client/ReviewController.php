<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ReviewRequest;
use App\Models\Order;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(Order $order): View
    {
        // Authorize the action
        $this->authorize('review', $order);

        // Ensure order is approved and doesn't have a review yet
        if ($order->status !== 'approved') {
            abort(403, 'Only approved orders can be reviewed.');
        }

        if ($order->review()->exists()) {
            abort(403, 'This order has already been reviewed.');
        }

        return view('client.reviews.create', [
            'order' => $order->load(['serviceListing', 'studentProfile.user']),
        ]);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(ReviewRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Find the order
        $order = Order::findOrFail($validated['order_id']);

        // Authorize the action
        $this->authorize('review', $order);

        try {
            // Create the review using the service
            $review = $this->reviewService->createReview($order, $validated);

            return redirect()
                ->route('client.orders.show', $order)
                ->with('success', 'Thank you for your review! Your feedback helps others make informed decisions.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to submit review. Please try again.');
        }
    }
}
