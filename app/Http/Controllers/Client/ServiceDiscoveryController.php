<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Services\ReviewService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceDiscoveryController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService,
        private readonly ReviewService $reviewService
    ) {}

    /**
     * Display service discovery page with search and filters.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'q',
            'category_id',
            'min_price',
            'max_price',
            'delivery_days',
            'min_rating',
            'sort',
        ]);

        $services = $this->searchService->searchServices($filters, 12);
        $categories = $this->searchService->getCategories();
        $priceRange = $this->searchService->getPriceRange();

        return view('client.services.index', compact('services', 'categories', 'priceRange', 'filters'));
    }

    /**
     * Display detailed service listing.
     */
    public function show(ServiceListing $service): View
    {
        // Only show active services
        if ($service->status !== 'active') {
            abort(404);
        }

        $service->load([
            'studentProfile.user',
            'category'
        ]);
        
        // Increment view count
        $service->incrementViews();

        $relatedServices = $this->searchService->getRelatedServices($service, 4);

        // Get reviews for this service
        $reviews = $this->reviewService->getServiceReviews($service, 10);

        $stats = [
            'total_orders' => $service->orders_count,
            'average_rating' => $service->average_rating,
            'total_reviews' => $reviews->total(),
        ];

        return view('client.services.show', compact('service', 'relatedServices', 'reviews', 'stats'));
    }
}
