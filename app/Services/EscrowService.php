<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * EscrowService manages fund holding and release for orders
 * Handles the escrow lifecycle:
 * - Holding funds when payment is received
 * - Releasing funds to student when order is approved
 * - Refunding funds to client when order is cancelled/disputed
 */
class EscrowService
{
    /**
     * Hold funds in escrow for an order
     *
     * @param Order $order The order to hold funds for
     * @return Transaction The escrow transaction
     */
    public function holdFunds(Order $order): Transaction
    {
        return DB::transaction(function () use ($order) {
            // Create escrow hold transaction
            $transaction = Transaction::create([
                'order_id' => $order->id,
                'user_id' => $order->studentProfile->user_id,
                'type' => 'escrow_hold',
                'amount' => $order->total_amount,
                'fee' => 0,
                'net_amount' => $order->total_amount,
                'status' => 'pending',
                'description' => "Escrow hold for Order #{$order->order_number}",
                'metadata' => [
                    'held_at' => now()->toIso8601String(),
                    'auto_release_at' => now()->addDays(7)->toIso8601String(),
                ],
            ]);

            // Update order escrow status
            $order->update([
                'escrow_status' => 'held',
            ]);

            return $transaction;
        });
    }

    /**
     * Release escrow funds to student
     *
     * @param Order $order The order to release funds for
     * @return Transaction The release transaction
     */
    public function releaseFunds(Order $order): Transaction
    {
        return DB::transaction(function () use ($order) {
            // Mark escrow hold as completed
            $escrowTransaction = $order->transactions()
                ->where('type', 'escrow_hold')
                ->where('status', 'pending')
                ->first();

            // If no escrow_hold transaction exists, create one retroactively
            // This can happen if the webhook didn't process properly
            if (!$escrowTransaction) {
                $escrowTransaction = $this->holdFunds($order);
            }

            $escrowTransaction->update([
                'status' => 'completed',
                'metadata' => array_merge($escrowTransaction->metadata ?? [], [
                    'released_at' => now()->toIso8601String(),
                ]),
            ]);

            // Calculate platform fee and student earnings
            $platformFee = app(PaymentService::class)->calculatePlatformFee($order->total_amount);
            $studentEarnings = app(PaymentService::class)->calculateStudentEarnings($order->total_amount);

            // Create platform fee transaction
            Transaction::create([
                'order_id' => $order->id,
                'user_id' => null, // Platform transaction
                'type' => 'platform_fee',
                'amount' => $platformFee,
                'fee' => 0,
                'net_amount' => $platformFee,
                'status' => 'completed',
                'metadata' => [
                    'fee_percentage' => 15,
                    'order_total' => $order->total_amount,
                ],
            ]);

            // Create student earnings transaction
            $earningsTransaction = Transaction::create([
                'order_id' => $order->id,
                'user_id' => $order->studentProfile->user_id,
                'type' => 'earnings',
                'amount' => $studentEarnings,
                'fee' => 0,
                'net_amount' => $studentEarnings,
                'status' => 'completed',
                'metadata' => [
                    'platform_fee' => $platformFee,
                    'gross_amount' => $order->total_amount,
                ],
            ]);

            // Update student profile's available balance
            $order->studentProfile->increment('available_balance', $studentEarnings);

            // Update order escrow and payment status
            $order->update([
                'escrow_status' => 'released',
                'payment_status' => 'paid',
            ]);

            return $earningsTransaction;
        });
    }

    /**
     * Refund escrow funds to client
     *
     * @param Order $order The order to refund
     * @param string|null $reason Reason for refund
     * @return Transaction The refund transaction
     */
    public function refundFunds(Order $order, ?string $reason = null): Transaction
    {
        return DB::transaction(function () use ($order, $reason) {
            // Mark escrow hold as cancelled
            $escrowTransaction = $order->transactions()
                ->where('type', 'escrow_hold')
                ->where('status', 'pending')
                ->first();

            // If no escrow_hold transaction exists, skip escrow cancellation
            // and proceed directly to Stripe refund
            if (!$escrowTransaction) {
                // Process Stripe refund
                $paymentService = app(PaymentService::class);
                $paymentService->processRefund($order, null, $reason);

                // Update order escrow status
                $order->update([
                    'escrow_status' => 'refunded',
                ]);

                // Return the refund transaction created by PaymentService
                return $order->transactions()
                    ->where('type', 'refund')
                    ->latest()
                    ->firstOrFail();
            }

            $escrowTransaction->update([
                'status' => 'cancelled',
                'metadata' => array_merge($escrowTransaction->metadata ?? [], [
                    'refunded_at' => now()->toIso8601String(),
                    'refund_reason' => $reason,
                ]),
            ]);

            // Process Stripe refund
            $paymentService = app(PaymentService::class);
            $paymentService->processRefund($order, null, $reason);

            // Update order escrow status
            $order->update([
                'escrow_status' => 'refunded',
            ]);

            // Return the refund transaction created by PaymentService
            return $order->transactions()
                ->where('type', 'refund')
                ->latest()
                ->firstOrFail();
        });
    }

    /**
     * Split escrow funds between student and client (for dispute resolution)
     *
     * @param Order $order The order to split funds for
     * @param float $studentAmount Amount to give to student
     * @param float $clientAmount Amount to refund to client
     * @param string|null $reason Reason for split
     * @return array Array of transactions [student_transaction, refund_transaction]
     */
    public function splitFunds(Order $order, float $studentAmount, float $clientAmount, ?string $reason = null): array
    {
        if ($studentAmount < 0 || $clientAmount < 0) {
            throw new \InvalidArgumentException('Split amounts cannot be negative');
        }

        if (abs(($studentAmount + $clientAmount) - $order->total_amount) > 0.01) {
            throw new \InvalidArgumentException('Split amounts must equal order total');
        }

        return DB::transaction(function () use ($order, $studentAmount, $clientAmount, $reason) {
            // Mark escrow hold as completed
            $escrowTransaction = $order->transactions()
                ->where('type', 'escrow_hold')
                ->where('status', 'pending')
                ->first();

            // If no escrow_hold transaction exists, create one retroactively
            if (!$escrowTransaction) {
                $escrowTransaction = $this->holdFunds($order);
            }

            $escrowTransaction->update([
                'status' => 'completed',
                'metadata' => array_merge($escrowTransaction->metadata ?? [], [
                    'split_at' => now()->toIso8601String(),
                    'split_reason' => $reason,
                    'student_amount' => $studentAmount,
                    'client_amount' => $clientAmount,
                ]),
            ]);

            // Use provided split amounts
            $refundAmount = $clientAmount;

            $transactions = [];

            // Create student earnings transaction if applicable
            if ($studentAmount > 0) {
                $platformFee = app(PaymentService::class)->calculatePlatformFee($studentAmount);
                $netStudentEarnings = $studentAmount - $platformFee;

                Transaction::create([
                    'order_id' => $order->id,
                    'user_id' => null,
                    'type' => 'platform_fee',
                    'amount' => $platformFee,
                    'fee' => 0,
                    'net_amount' => $platformFee,
                    'status' => 'completed',
                    'metadata' => [
                        'fee_percentage' => 15,
                        'split_amount' => $studentAmount,
                    ],
                ]);

                $transactions['student'] = Transaction::create([
                    'order_id' => $order->id,
                    'user_id' => $order->studentProfile->user_id,
                    'type' => 'earnings',
                    'amount' => $netStudentEarnings,
                    'fee' => 0,
                    'net_amount' => $netStudentEarnings,
                    'status' => 'completed',
                    'metadata' => [
                        'platform_fee' => $platformFee,
                        'gross_amount' => $studentAmount,
                        'split_percentage' => $studentPercentage,
                    ],
                ]);

                $order->studentProfile->increment('available_balance', $netStudentEarnings);
            }

            // Process refund to client if applicable
            if ($refundAmount > 0) {
                $paymentService = app(PaymentService::class);
                $transactions['refund'] = $paymentService->processRefund($order, $refundAmount, $reason);
            }

            // Update order escrow status
            $order->update([
                'escrow_status' => 'split',
            ]);

            return $transactions;
        });
    }

    /**
     * Check if escrow funds can be auto-released (7 days after delivery)
     *
     * @param Order $order The order to check
     * @return bool True if funds can be auto-released
     */
    public function canAutoRelease(Order $order): bool
    {
        if ($order->escrow_status !== 'held' || $order->status !== 'delivered') {
            return false;
        }

        $escrowTransaction = $order->transactions()
            ->where('type', 'escrow_hold')
            ->where('status', 'pending')
            ->first();

        if (!$escrowTransaction) {
            return false;
        }

        $autoReleaseAt = $escrowTransaction->metadata['auto_release_at'] ?? null;
        if (!$autoReleaseAt) {
            return false;
        }

        return now()->isAfter($autoReleaseAt);
    }

    /**
     * Get escrow status for an order
     *
     * @param Order $order The order to check
     * @return array Escrow status information
     */
    public function getEscrowStatus(Order $order): array
    {
        $escrowTransaction = $order->transactions()
            ->where('type', 'escrow_hold')
            ->first();

        if (!$escrowTransaction) {
            return [
                'status' => 'none',
                'amount' => 0,
                'held_at' => null,
                'auto_release_at' => null,
            ];
        }

        return [
            'status' => $order->escrow_status,
            'amount' => $escrowTransaction->amount,
            'held_at' => $escrowTransaction->metadata['held_at'] ?? null,
            'auto_release_at' => $escrowTransaction->metadata['auto_release_at'] ?? null,
            'released_at' => $escrowTransaction->metadata['released_at'] ?? null,
            'refunded_at' => $escrowTransaction->metadata['refunded_at'] ?? null,
        ];
    }

    /**
     * Update student balance after escrow release
     *
     * @param \App\Models\StudentProfile $student
     * @param float $amount
     * @return void
     */
    public function updateStudentBalance(\App\Models\StudentProfile $student, float $amount): void
    {
        DB::transaction(function () use ($student, $amount) {
            $student->increment('available_balance', $amount);
            $student->increment('total_earnings', $amount);
        });
    }

    /**
     * Get orders eligible for auto-release (completed > 7 days ago)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEligibleForAutoRelease(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('status', 'completed')
            ->where('escrow_status', 'held')
            ->where('completed_at', '<=', now()->subDays(7))
            ->get();
    }

    /**
     * Calculate platform commission
     *
     * @param float $amount
     * @return float Commission amount (15%)
     */
    public function calculateCommission(float $amount): float
    {
        return round($amount * 0.15, 2);
    }
}
