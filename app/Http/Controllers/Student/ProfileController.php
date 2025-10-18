<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Display the student's profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        // Load relationships for display
        $user->load(['studentProfile', 'serviceListings' => function ($query) {
            $query->where('status', 'active')->latest()->take(6);
        }]);

        // Get statistics
        $stats = [
            'total_orders' => $user->studentOrders()->count(),
            'completed_orders' => $user->studentOrders()->where('status', 'completed')->count(),
            'active_services' => $user->serviceListings()->where('status', 'active')->count(),
            'total_earnings' => $user->available_balance + $user->withdrawn_balance,
        ];

        return view('student.profile.show', compact('user', 'profile', 'stats'));
    }

    /**
     * Show the form for editing the student's profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        return view('student.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the student's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        DB::transaction(function () use ($request, $user, $profile) {
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($profile->profile_picture) {
                    $this->fileUploadService->deleteFile($profile->profile_picture);
                }

                $profilePicturePath = $this->fileUploadService->uploadProfilePicture(
                    $request->file('profile_picture'),
                    $user->id
                );

                $profile->profile_picture = $profilePicturePath;
            }

            // Handle portfolio files upload
            $portfolioFiles = $profile->portfolio_files ?? [];
            if ($request->hasFile('portfolio_files')) {
                $descriptions = $request->input('portfolio_descriptions', []);
                
                foreach ($request->file('portfolio_files') as $index => $file) {
                    $fileInfo = $this->fileUploadService->uploadPortfolioFile($file, $user->id);
                    
                    $portfolioFiles[] = [
                        'path' => $fileInfo['path'],
                        'type' => $fileInfo['type'],
                        'size' => $fileInfo['size'],
                        'original_name' => $fileInfo['original_name'],
                        'description' => $descriptions[$index] ?? null,
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            }

            // Update profile
            $profile->update([
                'bio' => $request->input('bio'),
                'tagline' => $request->input('tagline'),
                'university' => $request->input('university'),
                'field_of_study' => $request->input('field_of_study'),
                'year_of_study' => $request->input('year_of_study'),
                'skills' => $request->input('skills', []),
                'languages' => $request->input('languages', []),
                'github_url' => $request->input('github_url'),
                'linkedin_url' => $request->input('linkedin_url'),
                'portfolio_url' => $request->input('portfolio_url'),
                'behance_url' => $request->input('behance_url'),
                'portfolio_files' => $portfolioFiles,
                'available_for_work' => $request->boolean('available_for_work', true),
                'hourly_rate' => $request->input('hourly_rate'),
            ]);
        });

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete a portfolio file.
     */
    public function deletePortfolioFile(int $index): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        $portfolioFiles = $profile->portfolio_files ?? [];

        if (isset($portfolioFiles[$index])) {
            // Delete the file from storage
            $this->fileUploadService->deleteFile($portfolioFiles[$index]['path']);

            // Remove from array
            array_splice($portfolioFiles, $index, 1);

            // Update profile
            $profile->update([
                'portfolio_files' => array_values($portfolioFiles), // Re-index array
            ]);

            return redirect()->route('student.profile.edit')
                ->with('success', 'Portfolio file deleted successfully!');
        }

        return redirect()->route('student.profile.edit')
            ->with('error', 'Portfolio file not found.');
    }

    /**
     * Display public profile for a student.
     */
    public function publicProfile(int $userId): View
    {
        $user = \App\Models\User::with('studentProfile')->findOrFail($userId);

        if ($user->role !== 'student') {
            abort(404);
        }

        $profile = $user->studentProfile;

        // Get student's services
        $services = $user->serviceListings()
            ->where('status', 'active')
            ->latest()
            ->get();

        // Get reviews
        $reviews = $user->receivedReviews()
            ->with('client')
            ->latest()
            ->take(10)
            ->get();

        // Get statistics
        $stats = [
            'total_orders' => $user->studentOrders()->count(),
            'completed_orders' => $user->studentOrders()->where('status', 'completed')->count(),
            'active_services' => $user->serviceListings()->where('status', 'active')->count(),
        ];

        return view('student.profile.public', compact('user', 'profile', 'services', 'reviews', 'stats'));
    }
}
