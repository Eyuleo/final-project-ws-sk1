<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService
    ) {
    }

    /**
     * Display the earnings dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        // Get transaction history
        $transactions = $this->withdrawalService->getTransactionHistory($profile, 10);

        // Calculate earnings statistics
        $stats = [
            'available_balance' => $profile->available_balance,
            'pending_balance' => $profile->pending_balance,
            'withdrawn_balance' => $profile->withdrawn_balance,
            'total_earned' => $profile->available_balance + $profile->pending_balance + $profile->withdrawn_balance,
        ];

        // Get recent orders for earnings breakdown
        $recentOrders = $profile->orders()
            ->whereIn('status', ['completed', 'approved'])
            ->with('serviceListing')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.earnings.index', compact('profile', 'transactions', 'stats', 'recentOrders'));
    }

    /**
     * Show the form for creating a new withdrawal request.
     */
    public function createWithdrawal(): View
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        // Check if student has Stripe Connect account
        $hasStripeAccount = !empty($profile->stripe_connect_account_id);

        return view('student.earnings.withdraw', compact('profile', 'hasStripeAccount'));
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function storeWithdrawal(WithdrawalRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $profile = Auth::user()->studentProfile;

        try {
            $withdrawal = $this->withdrawalService->createWithdrawal($profile, $validated);

            return redirect()
                ->route('student.earnings.index')
                ->with('success', 'Withdrawal request submitted successfully! Your funds will be processed within 2-3 business days.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to process withdrawal request: ' . $e->getMessage());
        }
    }

    /**
     * Display withdrawal history.
     */
    public function withdrawals(): View
    {
        $profile = Auth::user()->studentProfile;
        $withdrawals = $this->withdrawalService->getWithdrawalHistory($profile, 15);

        return view('student.earnings.withdrawals', compact('withdrawals'));
    }

    /**
     * Generate Stripe Connect onboarding link.
     */
    public function connectStripe(): RedirectResponse
    {
        $profile = Auth::user()->studentProfile;

        $returnUrl = route('student.earnings.index') . '?stripe_connect=success';
        $refreshUrl = route('student.earnings.connect-stripe');

        $onboardingLink = $this->withdrawalService->generateOnboardingLink(
            $profile,
            $returnUrl,
            $refreshUrl
        );

        if (!$onboardingLink) {
            return back()->with('error', 'Failed to generate Stripe Connect onboarding link.');
        }

        return redirect($onboardingLink);
    }
}
