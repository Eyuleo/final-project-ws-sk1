<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService
    ) {}

    /**
     * Display all categories.
     */
    public function index(): View
    {
        $categories = $this->searchService->getCategories();
        $featuredServices = $this->searchService->getFeaturedServices(8);

        return view('categories.index', compact('categories', 'featuredServices'));
    }

    /**
     * Display services in a specific category.
     */
    public function show(\App\Models\Category $category, Request $request): View
    {
        $filters = array_merge(
            $request->only(['min_price', 'max_price', 'delivery_days', 'min_rating', 'sort']),
            ['category_id' => $category->id]
        );

        $services = $this->searchService->searchServices($filters, 12);
        $priceRange = $this->searchService->getPriceRange();
        $allCategories = $this->searchService->getCategories();

        return view('categories.show', compact('services', 'category', 'priceRange', 'filters', 'allCategories'));
    }
}
