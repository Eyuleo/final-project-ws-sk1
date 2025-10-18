<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ServiceListing;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected EscrowService $escrowService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Create a new order from service listing
     *
     * @param ServiceListing $service
     * @param ClientProfile $client
     * @param array $data ['requirements', 'quantity', 'deadline', 'attachment_files']
     * @return Order
     * @throws \Exception
     */
    public function createOrder(ServiceListing $service, ClientProfile $client, array $data): Order
    {
        if ($service->status !== 'active') {
            throw new \Exception('Service is not available for orders.');
        }

        $totals = $this->calculateOrderTotals($service, $data['quantity']);

        return DB::transaction(function () use ($service, $client, $data, $totals) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'service_listing_id' => $service->id,
                'student_profile_id' => $service->student_profile_id,
                'client_profile_id' => $client->id,
                'requirements' => $data['requirements'],
                'quantity' => $data['quantity'],
                'unit_price' => $service->price,
                'deadline' => $data['deadline'],
                'subtotal' => $totals['subtotal'],
                'platform_fee' => $totals['platform_fee'],
                'total_amount' => $totals['total_amount'],
                'status' => 'pending_payment',
                'escrow_status' => 'pending',
                'attachment_files' => $data['attachment_files'] ?? null,
                'max_revisions' => 2,
                'revision_count' => 0,
            ]);

            return $order;
        });
    }

    /**
     * Accept an order (student action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not in pending status
     */
    public function acceptOrder(Order $order): Order
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Only pending orders can be accepted.');
        }

        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            $this->notificationService->sendOrderAcceptedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Decline an order with reason (student action)
     *
     * @param Order $order
     * @param string $reason
     * @return Order
     * @throws \Exception if order not in pending status
     */
    public function declineOrder(Order $order, string $reason): Order
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Only pending orders can be declined.');
        }

        return DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => 'declined',
                'decline_reason' => $reason,
                'declined_at' => now(),
            ]);

            // Refund client
            if ($order->escrow_status === 'held') {
                $this->escrowService->refundFunds($order);
            }

            $this->notificationService->sendOrderDeclinedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Mark order as in progress (student action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not accepted
     */
    public function startWork(Order $order): Order
    {
        if ($order->status !== 'accepted') {
            throw new \Exception('Only accepted orders can be started.');
        }

        $order->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return $order->fresh();
    }

    /**
     * Upload deliverables and mark as completed (student action)
     *
     * @param Order $order
     * @param array $files Uploaded files
     * @param string|null $note Delivery note
     * @return Order
     * @throws \Exception if order not in progress
     */
    public function submitDeliverables(Order $order, array $files, ?string $note = null): Order
    {
        if (!in_array($order->status, ['accepted', 'in_progress', 'revision_requested'])) {
            throw new \Exception('Cannot submit deliverables for this order status.');
        }

        return DB::transaction(function () use ($order, $files, $note) {
            $order->update([
                'status' => 'completed',
                'deliverable_files' => $files,
                'delivery_note' => $note,
                'completed_at' => now(),
            ]);

            $this->notificationService->sendOrderCompletedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Approve order and release escrow (client action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not completed
     */
    public function approveOrder(Order $order): Order
    {
        if ($order->status !== 'completed') {
            throw new \Exception('Only completed orders can be approved.');
        }

        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Release escrow funds to student
            $this->escrowService->releaseFunds($order);

            $this->notificationService->sendOrderApprovedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Request revision (client action)
     *
     * @param Order $order
     * @param string $feedback
     * @return Order
     * @throws \Exception if max revisions exceeded or order not completed
     */
    public function requestRevision(Order $order, string $feedback): Order
    {
        if ($order->status !== 'completed') {
            throw new \Exception('Only completed orders can have revisions requested.');
        }

        if ($order->revision_count >= $order->max_revisions) {
            throw new \Exception('Maximum number of revisions exceeded.');
        }

        return DB::transaction(function () use ($order, $feedback) {
            $order->update([
                'status' => 'revision_requested',
                'revision_count' => $order->revision_count + 1,
                'revision_notes' => $feedback,
            ]);

            $this->notificationService->sendRevisionRequestedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Open dispute (client action)
     *
     * @param Order $order
     * @param string $reason
     * @param array $evidenceFiles
     * @return Order
     * @throws \Exception if revisions not exhausted
     */
    public function openDispute(Order $order, string $reason, array $evidenceFiles = []): Order
    {
        if ($order->revision_count < $order->max_revisions) {
            throw new \Exception('You must exhaust all revisions before opening a dispute.');
        }

        return DB::transaction(function () use ($order, $reason, $evidenceFiles) {
            $order->update([
                'status' => 'disputed',
                'dispute_reason' => $reason,
                'dispute_evidence' => $evidenceFiles,
                'disputed_at' => now(),
            ]);

            $this->notificationService->sendDisputeOpenedNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Cancel order (mutual or admin action)
     *
     * @param Order $order
     * @param string $reason
     * @param string $cancelledBy 'client', 'student', or 'admin'
     * @return Order
     * @throws \Exception if order cannot be cancelled
     */
    public function cancelOrder(Order $order, string $reason, string $cancelledBy): Order
    {
        if (in_array($order->status, ['approved', 'disputed', 'cancelled'])) {
            throw new \Exception('This order cannot be cancelled.');
        }

        return DB::transaction(function () use ($order, $reason, $cancelledBy) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by' => $cancelledBy,
                'cancelled_at' => now(),
            ]);

            // Refund if payment was made
            if ($order->escrow_status === 'held') {
                $this->escrowService->refundFunds($order);
            }

            $this->notificationService->sendOrderCancelledNotification($order);

            return $order->fresh();
        });
    }

    /**
     * Auto-release escrow after 7 days (scheduled job)
     *
     * @param Order $order
     * @return Order
     */
    public function autoReleaseEscrow(Order $order): Order
    {
        if ($order->status !== 'completed') {
            return $order;
        }

        $completedAt = $order->completed_at;
        if ($completedAt && $completedAt->diffInDays(now()) >= 7) {
            return $this->approveOrder($order);
        }

        return $order;
    }

    /**
     * Calculate order totals
     *
     * @param ServiceListing $service
     * @param int $quantity
     * @return array ['subtotal', 'platform_fee', 'total_amount']
     */
    public function calculateOrderTotals(ServiceListing $service, int $quantity): array
    {
        $subtotal = $service->price * $quantity;
        $platformFee = $this->escrowService->calculateCommission($subtotal);
        $totalAmount = $subtotal + $platformFee;

        return [
            'subtotal' => round($subtotal, 2),
            'platform_fee' => round($platformFee, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
