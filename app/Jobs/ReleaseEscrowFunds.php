<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseEscrowFunds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Execute the job.
     * Automatically release escrow funds for completed orders after 7 days.
     */
    public function handle(NotificationService $notificationService): void
    {
        Log::info('ReleaseEscrowFunds job started');

        // Find orders that are completed and have been in that state for 7+ days
        $orders = Order::where('status', 'completed')
            ->where('completed_at', '<=', now()->subDays(7))
            ->whereDoesntHave('transactions', function ($query) {
                $query->where('type', 'escrow_release')
                    ->where('status', 'completed');
            })
            ->get();

        Log::info('Found orders to auto-release', [
            'count' => $orders->count(),
        ]);

        foreach ($orders as $order) {
            try {
                $this->releaseEscrowForOrder($order, $notificationService);
            } catch (\Exception $e) {
                Log::error('Failed to auto-release escrow for order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue with next order
            }
        }

        Log::info('ReleaseEscrowFunds job completed');
    }

    /**
     * Release escrow funds for a specific order.
     *
     * @param Order $order
     * @param NotificationService $notificationService
     * @return void
     * @throws \Exception
     */
    protected function releaseEscrowForOrder(Order $order, NotificationService $notificationService): void
    {
        DB::beginTransaction();

        try {
            // Calculate platform commission (15%)
            $commissionRate = 0.15;
            $commission = $order->student_earnings * $commissionRate;
            $netEarnings = $order->student_earnings - $commission;

            // Update order status to approved (auto-approved)
            $order->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Create escrow release transaction
            Transaction::create([
                'user_id' => $order->studentProfile->user_id,
                'order_id' => $order->id,
                'type' => 'escrow_release',
                'amount' => $order->student_earnings,
                'fee' => $commission,
                'net_amount' => $netEarnings,
                'status' => 'completed',
                'description' => 'Auto-release of escrow funds for Order #' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'commission_rate' => $commissionRate,
                    'auto_released' => true,
                ],
            ]);

            // Create commission transaction for platform
            Transaction::create([
                'user_id' => $order->studentProfile->user_id,
                'order_id' => $order->id,
                'type' => 'commission',
                'amount' => $commission,
                'fee' => 0,
                'net_amount' => $commission,
                'status' => 'completed',
                'description' => 'Platform commission for Order #' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'commission_rate' => $commissionRate,
                ],
            ]);

            // Update student profile balances
            $studentProfile = $order->studentProfile;
            $studentProfile->decrement('pending_balance', $order->student_earnings);
            $studentProfile->increment('available_balance', $netEarnings);

            DB::commit();

            // Send notifications to both client and student
            $notificationService->sendOrderNotification(
                $order,
                'auto_approved',
                'Order automatically approved after 7 days. Funds have been released to the service provider.'
            );

            Log::info('Escrow funds auto-released for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'net_earnings' => $netEarnings,
                'commission' => $commission,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to release escrow for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ReleaseEscrowFunds job permanently failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
