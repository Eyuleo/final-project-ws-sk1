<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Display the client's profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->clientProfile;

        // Get statistics
        $stats = [
            'total_orders' => $profile->orders()->count(),
            'completed_orders' => $profile->orders()->where('status', 'approved')->count(),
            'active_orders' => $profile->orders()->whereIn('status', ['pending', 'accepted', 'in_progress', 'completed'])->count(),
            'total_spent' => $profile->orders()->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('client.profile.show', compact('user', 'profile', 'stats'));
    }

    /**
     * Show the form for editing the client's profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->clientProfile;

        return view('client.profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the client's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'organization' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $profile = $user->clientProfile;

        DB::transaction(function () use ($request, $user, $profile) {
            // Update user info
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            // Prepare profile data
            $profileData = [
                'organization' => $request->input('organization'),
                'bio' => $request->input('bio'),
            ];

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

                $profileData['profile_picture'] = $profilePicturePath;
            }

            // Update profile
            $profile->update($profileData);
        });

        return redirect()->route('client.profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
