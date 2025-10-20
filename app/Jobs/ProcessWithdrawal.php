<?php

namespace App\Jobs;

use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Withdrawal $withdrawal
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WithdrawalService $withdrawalService, NotificationService $notificationService): void
    {
        Log::info('Processing withdrawal job started', [
            'withdrawal_id' => $this->withdrawal->id,
        ]);

        try {
            // Process the withdrawal via Stripe
            $success = $withdrawalService->processWithdrawal($this->withdrawal);

            if ($success) {
                // Send success notification
                $notificationService->sendWithdrawalNotification(
                    $this->withdrawal,
                    'completed'
                );

                Log::info('Withdrawal processed successfully', [
                    'withdrawal_id' => $this->withdrawal->id,
                ]);
            } else {
                // Send failure notification
                $notificationService->sendWithdrawalNotification(
                    $this->withdrawal,
                    'failed'
                );

                Log::warning('Withdrawal processing failed', [
                    'withdrawal_id' => $this->withdrawal->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Withdrawal processing job failed', [
                'withdrawal_id' => $this->withdrawal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Send failure notification
            $notificationService->sendWithdrawalNotification(
                $this->withdrawal,
                'failed'
            );

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Withdrawal processing job permanently failed', [
            'withdrawal_id' => $this->withdrawal->id,
            'error' => $exception->getMessage(),
        ]);

        // Mark withdrawal as failed if not already
        if ($this->withdrawal->status !== 'failed') {
            $this->withdrawal->update([
                'status' => 'failed',
                'notes' => 'Processing failed after multiple attempts: ' . $exception->getMessage(),
            ]);

            // Refund to available balance
            $this->withdrawal->studentProfile->increment('available_balance', $this->withdrawal->amount);
        }
    }
}
