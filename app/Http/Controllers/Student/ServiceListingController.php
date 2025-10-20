<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateServiceRequest;
use App\Http\Requests\Student\UpdateServiceRequest;
use App\Models\ServiceListing;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceListingController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
        // Authorization handled by student middleware
    }

    /**
     * Display a listing of the student's services.
     */
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->studentProfile;
        
        $services = $profile->serviceListings()
            ->with('category')
            ->withCount(['orders', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        $stats = [
            'total_services' => $profile->serviceListings()->count(),
            'active_services' => $profile->serviceListings()->where('status', 'active')->count(),
            'paused_services' => $profile->serviceListings()->where('status', 'paused')->count(),
            'total_orders' => $profile->orders()->count(),
        ];

        return view('student.services.index', compact('services', 'stats'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();

        return view('student.services.create', compact('categories'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(CreateServiceRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Handle portfolio samples upload
        $portfolioSamples = [];
        if ($request->hasFile('portfolio_samples')) {
            foreach ($request->file('portfolio_samples') as $index => $file) {
                $uploadedFile = $this->fileUploadService->uploadFile(
                    $file,
                    'service-samples',
                    true // Generate thumbnail for images
                );

                $portfolioSamples[] = [
                    'path' => $uploadedFile['path'],
                    'thumbnail' => $uploadedFile['thumbnail'] ?? null,
                    'original_name' => $uploadedFile['original_name'],
                    'type' => $uploadedFile['type'],
                    'size' => $uploadedFile['size'],
                    'description' => $request->input("sample_descriptions.{$index}"),
                ];
            }
        }

        // Create service listing
        $service = $user->studentProfile->serviceListings()->create([
            'title' => $validated['title'],
            'category_id' => $validated['category'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']) . '-' . time(),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'delivery_days' => $validated['delivery_time'],
            'revisions' => $validated['revisions'],
            'requirements' => $validated['requirements'] ?? null,
            'portfolio_files' => $portfolioSamples,
            'status' => 'draft',
        ]);

        return redirect()->route('student.services.show', $service)
            ->with('success', 'Service listing created successfully!');
    }

    /**
     * Display the specified service.
     */
    public function show(ServiceListing $service): View
    {
        $service->load(['studentProfile.user', 'category']);
        
        $stats = [
            'total_orders' => $service->orders()->count(),
            'completed_orders' => $service->orders()->where('status', 'completed')->count(),
            'in_progress_orders' => $service->orders()->whereIn('status', ['pending', 'in_progress'])->count(),
            'average_rating' => $service->reviews()->avg('rating') ?? 0,
            'total_reviews' => $service->reviews()->count(),
        ];

        return view('student.services.show', compact('service', 'stats'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(ServiceListing $service): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();

        return view('student.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(UpdateServiceRequest $request, ServiceListing $service): RedirectResponse
    {
        $validated = $request->validated();

        // Handle new portfolio samples upload
        $existingSamples = $service->portfolio_files ?? [];
        $newSamples = [];

        if ($request->hasFile('portfolio_samples')) {
            foreach ($request->file('portfolio_samples') as $index => $file) {
                $uploadedFile = $this->fileUploadService->uploadFile(
                    $file,
                    'service-samples',
                    true
                );

                $newSamples[] = [
                    'path' => $uploadedFile['path'],
                    'thumbnail' => $uploadedFile['thumbnail'] ?? null,
                    'original_name' => $uploadedFile['original_name'],
                    'type' => $uploadedFile['type'],
                    'size' => $uploadedFile['size'],
                    'description' => $request->input("sample_descriptions.{$index}"),
                ];
            }
        }

        // Merge existing and new samples
        $portfolioSamples = array_merge($existingSamples, $newSamples);

        // Update service
        $service->update([
            'title' => $validated['title'],
            'category_id' => $validated['category'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']) . '-' . $service->id,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'delivery_days' => $validated['delivery_time'],
            'revisions' => $validated['revisions'],
            'requirements' => $validated['requirements'] ?? null,
            'portfolio_files' => $portfolioSamples,
        ]);

        return redirect()->route('student.services.show', $service)
            ->with('success', 'Service listing updated successfully!');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(ServiceListing $service): RedirectResponse
    {
        // Check if service has active orders
        if ($service->orders()->whereIn('status', ['pending', 'in_progress'])->exists()) {
            return redirect()->route('student.services.index')
                ->with('error', 'Cannot delete service with active orders. Please complete or cancel them first.');
        }

        // Delete portfolio samples
        if ($service->portfolio_files) {
            foreach ($service->portfolio_files as $sample) {
                $this->fileUploadService->deleteFile($sample['path']);
                if (isset($sample['thumbnail'])) {
                    $this->fileUploadService->deleteFile($sample['thumbnail']);
                }
            }
        }

        $service->delete();

        return redirect()->route('student.services.index')
            ->with('success', 'Service listing deleted successfully!');
    }

    /**
     * Toggle service status between active and paused.
     */
    public function toggleStatus(ServiceListing $service): RedirectResponse
    {
        // Check authorization
        $user = Auth::user();
        if (!$user->studentProfile || $service->student_profile_id !== $user->studentProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $newStatus = $service->status === 'active' ? 'paused' : 'active';
        $service->update(['status' => $newStatus]);

        $message = $newStatus === 'active' 
            ? 'Service activated successfully!' 
            : 'Service paused successfully!';

        return back()->with('success', $message);
    }

    /**
     * Delete a portfolio sample from service.
     */
    public function deleteSample(ServiceListing $service, int $index): RedirectResponse
    {
        // Check authorization
        $user = Auth::user();
        if (!$user->studentProfile || $service->student_profile_id !== $user->studentProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $samples = $service->portfolio_files ?? [];

        if (isset($samples[$index])) {
            // Delete the file from storage
            $this->fileUploadService->deleteFile($samples[$index]['path']);
            if (isset($samples[$index]['thumbnail'])) {
                $this->fileUploadService->deleteFile($samples[$index]['thumbnail']);
            }

            // Remove from array
            array_splice($samples, $index, 1);

            // Update service
            $service->update([
                'portfolio_files' => array_values($samples), // Re-index array
            ]);

            return back()->with('success', 'Portfolio sample deleted successfully!');
        }

        return back()->with('error', 'Portfolio sample not found.');
    }
}
