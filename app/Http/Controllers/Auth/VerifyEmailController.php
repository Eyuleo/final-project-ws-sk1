<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectToRoleDashboard($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectToRoleDashboard($request->user());
    }

    /**
     * Redirect user to their role-specific dashboard after email verification.
     */
    private function redirectToRoleDashboard($user): RedirectResponse
    {
        $message = 'Your email has been verified successfully!';
        
        return match ($user->role) {
            'student' => redirect()->route('student.profile.edit')->with('success', $message . ' Please complete your profile to start offering services.'),
            'client' => redirect()->route('client.dashboard')->with('success', $message),
            'admin' => redirect()->route('admin.dashboard')->with('success', $message),
            default => redirect()->route('dashboard')->with('success', $message),
        };
    }
}
