<?php

namespace App\Services;

use App\Models\Withdrawal;
use App\Models\StudentProfile;
use App\Models\Transaction;
use App\Jobs\ProcessWithdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class WithdrawalService
{
    protected ?StripeClient $stripe;

    public function __construct()
    {
        $stripeSecret = config('services.stripe.secret');
        
        // Only initialize Stripe if secret key is configured
        if ($stripeSecret) {
            $this->stripe = new StripeClient($stripeSecret);
        } else {
            $this->stripe = null;
            Log::warning('Stripe secret key not configured. Withdrawal features will be limited.');
        }
    }

    /**
     * Create a withdrawal request.
     *
     * @param StudentProfile $studentProfile
     * @param array $data
     * @return Withdrawal
     * @throws \Exception
     */
    public function createWithdrawal(StudentProfile $studentProfile, array $data): Withdrawal
    {
        // Validate available balance
        if ($studentProfile->available_balance < $data['amount']) {
            throw new \Exception('Insufficient balance for withdrawal.');
        }

        try {
            DB::beginTransaction();

            // Calculate fees (2% processing fee, minimum $1)
            $fee = max(1, $data['amount'] * 0.02);
            $netAmount = $data['amount'] - $fee;

            // Prepare account details
            $accountDetails = $this->prepareAccountDetails($data);

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'student_profile_id' => $studentProfile->id,
                'amount' => $data['amount'],
                'fee' => $fee,
                'net_amount' => $netAmount,
                'method' => $data['method'],
                'account_details' => $accountDetails,
                'status' => 'pending',
            ]);

            // Deduct from available balance
            $studentProfile->decrement('available_balance', $data['amount']);

            // Create transaction record
            Transaction::create([
                'user_id' => $studentProfile->user_id,
                'type' => 'withdrawal',
                'amount' => $data['amount'],
                'fee' => $fee,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'description' => 'Withdrawal request via ' . str_replace('_', ' ', $data['method']),
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'method' => $data['method'],
                ],
            ]);

            DB::commit();

            // Dispatch job to process withdrawal
            ProcessWithdrawal::dispatch($withdrawal);

            Log::info('Withdrawal request created', [
                'withdrawal_id' => $withdrawal->id,
                'student_profile_id' => $studentProfile->id,
                'amount' => $data['amount'],
            ]);

            return $withdrawal;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create withdrawal', [
                'student_profile_id' => $studentProfile->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Prepare account details for storage.
     *
     * @param array $data
     * @return array
     */
    protected function prepareAccountDetails(array $data): array
    {
        if ($data['method'] === 'bank_transfer') {
            return [
                'account_number' => $data['account_number'],
                'bank_name' => $data['bank_name'],
                'account_holder_name' => $data['account_holder_name'],
            ];
        }

        return [
            'phone_number' => $data['phone_number'],
            'mobile_provider' => $data['mobile_provider'],
        ];
    }

    /**
     * Create or retrieve Stripe Connect account for a student.
     *
     * @param StudentProfile $studentProfile
     * @return string|null Stripe account ID
     */
    public function createOrGetStripeConnectAccount(StudentProfile $studentProfile): ?string
    {
        // If student already has a Stripe account ID, return it
        if ($studentProfile->stripe_connect_id) {
            return $studentProfile->stripe_connect_id;
        }

        // Check if Stripe is configured
        if (!$this->stripe) {
            throw new \Exception('Stripe is not configured. Please add STRIPE_SECRET to your .env file.');
        }

        try {
            // Create Stripe Connect account
            $account = $this->stripe->accounts->create([
                'type' => 'express',
                'country' => 'US', // Change based on your target country
                'email' => $studentProfile->user->email,
                'capabilities' => [
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
                'individual' => [
                    'email' => $studentProfile->user->email,
                    'first_name' => explode(' ', $studentProfile->user->name)[0] ?? $studentProfile->user->name,
                    'last_name' => explode(' ', $studentProfile->user->name)[1] ?? '',
                ],
            ]);

            // Save Stripe account ID to student profile
            $studentProfile->update([
                'stripe_connect_id' => $account->id,
            ]);

            Log::info('Stripe Connect account created', [
                'student_profile_id' => $studentProfile->id,
                'stripe_account_id' => $account->id,
            ]);

            return $account->id;
        } catch (ApiErrorException $e) {
            Log::error('Failed to create Stripe Connect account', [
                'student_profile_id' => $studentProfile->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate Stripe Connect onboarding link.
     *
     * @param StudentProfile $studentProfile
     * @param string $returnUrl
     * @param string $refreshUrl
     * @return string|null
     */
    public function generateOnboardingLink(
        StudentProfile $studentProfile,
        string $returnUrl,
        string $refreshUrl
    ): ?string {
        // Check if Stripe is configured
        if (!$this->stripe) {
            throw new \Exception('Stripe is not configured. Please add STRIPE_SECRET to your .env file.');
        }

        $accountId = $this->createOrGetStripeConnectAccount($studentProfile);

        if (!$accountId) {
            return null;
        }

        try {
            $accountLink = $this->stripe->accountLinks->create([
                'account' => $accountId,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            return $accountLink->url;
        } catch (ApiErrorException $e) {
            Log::error('Failed to generate onboarding link', [
                'student_profile_id' => $studentProfile->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Process withdrawal via Stripe.
     *
     * @param Withdrawal $withdrawal
     * @return bool
     */
    public function processWithdrawal(Withdrawal $withdrawal): bool
    {
        $studentProfile = $withdrawal->studentProfile;

        // Check if Stripe is configured
        if (!$this->stripe) {
            throw new \Exception('Stripe is not configured. Please add STRIPE_SECRET to your .env file.');
        }

        try {
            // Update status to processing
            $withdrawal->update(['status' => 'processing']);

            // Get or create Stripe Connect account
            $accountId = $this->createOrGetStripeConnectAccount($studentProfile);

            if (!$accountId) {
                throw new \Exception('No Stripe Connect account for student.');
            }

            // Create payout (convert to cents)
            $amountInCents = (int) ($withdrawal->net_amount * 100);

            $payout = $this->stripe->payouts->create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'description' => 'Withdrawal #' . $withdrawal->id,
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'student_profile_id' => $studentProfile->id,
                ],
            ], [
                'stripe_account' => $accountId,
            ]);

            // Update withdrawal with Stripe payout ID
            $withdrawal->update([
                'stripe_payout_id' => $payout->id,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // Update transaction status
            Transaction::where('metadata->withdrawal_id', $withdrawal->id)
                ->update(['status' => 'completed']);

            // Update withdrawn balance
            $studentProfile->increment('withdrawn_balance', $withdrawal->amount);

            Log::info('Withdrawal processed successfully', [
                'withdrawal_id' => $withdrawal->id,
                'payout_id' => $payout->id,
            ]);

            return true;
        } catch (\Exception $e) {
            // Mark withdrawal as failed
            $withdrawal->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            // Refund to available balance
            $studentProfile->increment('available_balance', $withdrawal->amount);

            // Update transaction status
            Transaction::where('metadata->withdrawal_id', $withdrawal->id)
                ->update(['status' => 'failed']);

            Log::error('Withdrawal processing failed', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get withdrawal history for a student.
     *
     * @param StudentProfile $studentProfile
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWithdrawalHistory(StudentProfile $studentProfile, int $perPage = 15)
    {
        return Withdrawal::where('student_profile_id', $studentProfile->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get transaction history for a student.
     *
     * @param StudentProfile $studentProfile
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTransactionHistory(StudentProfile $studentProfile, int $perPage = 20)
    {
        return Transaction::where('user_id', $studentProfile->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Cancel a pending withdrawal.
     *
     * @param Withdrawal $withdrawal
     * @return bool
     */
    public function cancelWithdrawal(Withdrawal $withdrawal): bool
    {
        if ($withdrawal->status !== 'pending') {
            return false;
        }

        try {
            DB::beginTransaction();

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'cancelled',
                'notes' => 'Cancelled by user',
            ]);

            // Refund to available balance
            $withdrawal->studentProfile->increment('available_balance', $withdrawal->amount);

            // Update transaction status
            Transaction::where('metadata->withdrawal_id', $withdrawal->id)
                ->update(['status' => 'cancelled']);

            DB::commit();

            Log::info('Withdrawal cancelled', [
                'withdrawal_id' => $withdrawal->id,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel withdrawal', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
