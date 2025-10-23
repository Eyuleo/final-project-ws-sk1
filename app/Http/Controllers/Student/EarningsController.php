<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\WithdrawalRequest;
use App\Services\ExportService;
use App\Services\WithdrawalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EarningsController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService,
        protected ExportService $exportService
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
        $hasStripeAccount = !empty($profile->stripe_connect_id);

        // Check Stripe verification status
        $stripeVerified = false;
        $stripeMessage = '';
        
        if ($hasStripeAccount) {
            $stripeStatus = $this->withdrawalService->checkStripeAccountStatus($profile);
            $stripeVerified = $stripeStatus['isVerified'];
            $stripeMessage = $stripeStatus['message'];
        }

        return view('student.earnings.withdraw', compact('profile', 'hasStripeAccount', 'stripeVerified', 'stripeMessage'));
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

    /**
     * Export transaction history.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $format = $request->input('format', 'csv');
        
        // Validate format
        if (!in_array($format, ['csv', 'json'])) {
            abort(400, 'Invalid export format');
        }
        
        // Get filters from request
        $filters = $request->only(['date_from', 'date_to', 'type']);
        
        // Generate export content
        $content = $format === 'csv' 
            ? $this->exportService->exportTransactionsToCSV($user, $filters)
            : $this->exportService->exportTransactionsToJSON($user, $filters);
        
        // Generate filename
        $filename = $this->exportService->generateExportFilename($format, $user);
        
        // Return streamed response
        return response()->streamDownload(
            function () use ($content) {
                echo $content;
            },
            $filename,
            [
                'Content-Type' => $this->exportService->getMimeType($format),
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
