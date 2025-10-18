<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public string $notificationType
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        match ($this->notificationType) {
            'order_placed' => $notificationService->sendOrderPlacedNotification($this->order),
            'order_accepted' => $notificationService->sendOrderAcceptedNotification($this->order),
            'order_declined' => $notificationService->sendOrderDeclinedNotification($this->order),
            'order_completed' => $notificationService->sendOrderCompletedNotification($this->order),
            'order_approved' => $notificationService->sendOrderApprovedNotification($this->order),
            'revision_requested' => $notificationService->sendRevisionRequestedNotification($this->order),
            'dispute_opened' => $notificationService->sendDisputeOpenedNotification($this->order),
            'order_cancelled' => $notificationService->sendOrderCancelledNotification($this->order),
            default => throw new \InvalidArgumentException("Unknown notification type: {$this->notificationType}"),
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Order notification failed', [
            'order_id' => $this->order->id,
            'notification_type' => $this->notificationType,
            'error' => $exception->getMessage(),
        ]);
    }
}
