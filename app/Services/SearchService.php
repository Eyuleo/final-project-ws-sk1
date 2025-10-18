<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ServiceListing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Search and filter service listings.
     */
    public function searchServices(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = ServiceListing::query()
            ->where('status', 'active')
            ->with(['studentProfile.user', 'category']);

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        // Apply sorting
        $query = $this->applySorting($query, $filters['sort'] ?? 'relevance');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Apply filters to the query.
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        // Search query
        if (!empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Delivery time filter
        if (!empty($filters['delivery_days'])) {
            $deliveryDays = (int) $filters['delivery_days'];
            $query->where('delivery_days', '<=', $deliveryDays);
        }

        // Rating filter
        if (!empty($filters['min_rating'])) {
            $minRating = (float) $filters['min_rating'];
            $query->where('average_rating', '>=', $minRating);
        }

        // Student filter (for viewing specific student's services)
        if (!empty($filters['student_profile_id'])) {
            $query->where('student_profile_id', $filters['student_profile_id']);
        }

        return $query;
    }

    /**
     * Apply sorting to the query.
     */
    protected function applySorting(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('average_rating'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'popular' => $query->orderByDesc('orders_count'),
            'delivery' => $query->orderBy('delivery_days', 'asc'),
            default => $query->orderByDesc('created_at'), // relevance/default
        };
    }

    /**
     * Get available categories with service counts.
     */
    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Category::query()
            ->withCount(['serviceListings' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get featured services.
     */
    public function getFeaturedServices(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceListing::where('status', 'active')
            ->with(['studentProfile.user', 'category'])
            ->orderByDesc('orders_count')
            ->orderByDesc('average_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related services based on category.
     */
    public function getRelatedServices(ServiceListing $service, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceListing::where('status', 'active')
            ->where('id', '!=', $service->id)
            ->where('category_id', $service->category_id)
            ->with(['studentProfile.user', 'category'])
            ->orderByDesc('average_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get price range statistics.
     */
    public function getPriceRange(): array
    {
        $stats = ServiceListing::where('status', 'active')
            ->selectRaw('MIN(price) as min, MAX(price) as max, AVG(price) as avg')
            ->first();

        return [
            'min' => $stats->min ?? 0,
            'max' => $stats->max ?? 1000,
            'avg' => $stats->avg ?? 50,
        ];
    }
}
