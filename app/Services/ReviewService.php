<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Order;
use App\Models\StudentProfile;
use App\Models\ServiceListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    /**
     * Create a new review for an order.
     *
     * @param Order $order
     * @param array $data
     * @return Review
     * @throws \Exception
     */
    public function createReview(Order $order, array $data): Review
    {
        try {
            DB::beginTransaction();

            // Create the review
            $review = Review::create([
                'order_id' => $order->id,
                'reviewer_id' => $order->clientProfile->user_id,
                'reviewed_id' => $order->studentProfile->user_id,
                'rating' => $data['rating'],
                'review_text' => $data['review_text'] ?? null,
                'tags' => isset($data['tags']) ? $data['tags'] : null,
                'is_visible' => true,
            ]);

            // Update rating aggregations
            $this->updateStudentRating($order->studentProfile);
            $this->updateServiceListingRating($order->serviceListing);

            // Update review counts
            $this->updateReviewCounts($order->studentProfile);

            DB::commit();

            Log::info('Review created successfully', [
                'review_id' => $review->id,
                'order_id' => $order->id,
                'rating' => $data['rating'],
            ]);

            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create review', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update the average rating for a student profile.
     *
     * @param StudentProfile $studentProfile
     * @return void
     */
    public function updateStudentRating(StudentProfile $studentProfile): void
    {
        $averageRating = Review::where('reviewed_id', $studentProfile->user_id)
            ->where('is_visible', true)
            ->avg('rating');

        $studentProfile->update([
            'average_rating' => round($averageRating ?? 0, 2),
        ]);

        Log::info('Updated student profile rating', [
            'student_profile_id' => $studentProfile->id,
            'average_rating' => $studentProfile->average_rating,
        ]);
    }

    /**
     * Update the average rating for a service listing.
     *
     * @param ServiceListing $serviceListing
     * @return void
     */
    public function updateServiceListingRating(ServiceListing $serviceListing): void
    {
        $averageRating = $serviceListing->orders()
            ->whereHas('review', function ($query) {
                $query->where('is_visible', true);
            })
            ->join('reviews', 'orders.id', '=', 'reviews.order_id')
            ->avg('reviews.rating');

        $serviceListing->update([
            'average_rating' => round($averageRating ?? 0, 2),
        ]);

        Log::info('Updated service listing rating', [
            'service_listing_id' => $serviceListing->id,
            'average_rating' => $serviceListing->average_rating,
        ]);
    }

    /**
     * Update the review counts for a student profile.
     *
     * @param StudentProfile $studentProfile
     * @return void
     */
    public function updateReviewCounts(StudentProfile $studentProfile): void
    {
        $totalReviews = Review::where('reviewed_id', $studentProfile->user_id)
            ->where('is_visible', true)
            ->count();

        $studentProfile->update([
            'total_reviews' => $totalReviews,
        ]);

        Log::info('Updated student profile review count', [
            'student_profile_id' => $studentProfile->id,
            'total_reviews' => $totalReviews,
        ]);
    }

    /**
     * Get reviews for a student profile.
     *
     * @param StudentProfile $studentProfile
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getStudentReviews(StudentProfile $studentProfile, int $perPage = 10)
    {
        return Review::where('reviewed_id', $studentProfile->user_id)
            ->where('is_visible', true)
            ->with(['reviewer', 'order.serviceListing'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get reviews for a service listing.
     *
     * @param ServiceListing $serviceListing
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getServiceReviews(ServiceListing $serviceListing, int $perPage = 10)
    {
        return Review::whereHas('order', function ($query) use ($serviceListing) {
                $query->where('service_listing_id', $serviceListing->id);
            })
            ->where('is_visible', true)
            ->with(['reviewer', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get rating breakdown for a student profile.
     *
     * @param StudentProfile $studentProfile
     * @return array
     */
    public function getRatingBreakdown(StudentProfile $studentProfile): array
    {
        $breakdown = Review::where('reviewed_id', $studentProfile->user_id)
            ->where('is_visible', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are present
        $result = [];
        for ($i = 5; $i >= 1; $i--) {
            $result[$i] = $breakdown[$i] ?? 0;
        }

        return $result;
    }

    /**
     * Get most common tags for a student profile.
     *
     * @param StudentProfile $studentProfile
     * @param int $limit
     * @return array
     */
    public function getCommonTags(StudentProfile $studentProfile, int $limit = 5): array
    {
        $reviews = Review::where('reviewed_id', $studentProfile->user_id)
            ->where('is_visible', true)
            ->whereNotNull('tags')
            ->get();

        $tagCounts = [];
        foreach ($reviews as $review) {
            if (is_array($review->tags)) {
                foreach ($review->tags as $tag) {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }

        arsort($tagCounts);
        return array_slice($tagCounts, 0, $limit, true);
    }

    /**
     * Check if a user can review an order.
     *
     * @param Order $order
     * @param int $userId
     * @return bool
     */
    public function canReviewOrder(Order $order, int $userId): bool
    {
        // Order must be approved
        if ($order->status !== 'approved') {
            return false;
        }

        // User must be the client
        if ($order->clientProfile->user_id !== $userId) {
            return false;
        }

        // Order must not already have a review
        if ($order->review()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Hide a review (admin moderation).
     *
     * @param Review $review
     * @param string $reason
     * @return void
     */
    public function hideReview(Review $review, string $reason = ''): void
    {
        DB::beginTransaction();

        try {
            $review->update(['is_visible' => false]);

            // Recalculate ratings
            $studentProfile = StudentProfile::where('user_id', $review->reviewed_id)->first();
            if ($studentProfile) {
                $this->updateStudentRating($studentProfile);
                $this->updateReviewCounts($studentProfile);
            }

            $order = $review->order;
            if ($order && $order->serviceListing) {
                $this->updateServiceListingRating($order->serviceListing);
            }

            DB::commit();

            Log::info('Review hidden', [
                'review_id' => $review->id,
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to hide review', [
                'review_id' => $review->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Show a review (restore visibility).
     *
     * @param Review $review
     * @return void
     */
    public function showReview(Review $review): void
    {
        DB::beginTransaction();

        try {
            $review->update(['is_visible' => true]);

            // Recalculate ratings
            $studentProfile = StudentProfile::where('user_id', $review->reviewed_id)->first();
            if ($studentProfile) {
                $this->updateStudentRating($studentProfile);
                $this->updateReviewCounts($studentProfile);
            }

            $order = $review->order;
            if ($order && $order->serviceListing) {
                $this->updateServiceListingRating($order->serviceListing);
            }

            DB::commit();

            Log::info('Review restored', [
                'review_id' => $review->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore review', [
                'review_id' => $review->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
