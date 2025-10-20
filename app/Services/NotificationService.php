<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * NotificationService handles all notification operations including:
 * - Email notifications
 * - In-app notifications
 * - Real-time notifications via broadcasting
 */
class NotificationService
{
    /**
     * Send order placed notification to student
     *
     * @param Order $order The order that was placed
     * @return void
     */
    public function sendOrderPlacedNotification(Order $order): void
    {
        \Log::info('sendOrderPlacedNotification called', ['order_id' => $order->id]);
        
        $student = $order->studentProfile->user;
        $client = $order->clientProfile->user;

        \Log::info('Preparing to send student email', [
            'student_email' => $student->email,
            'order_number' => $order->order_number,
        ]);

        // Send email notification
        try {
            Mail::send('emails.orders.placed-student', [
                'student' => $student,
                'client' => $client,
                'order' => $order,
                'service' => $order->serviceListing,
            ], function ($message) use ($student, $order) {
                $message->to($student->email)
                    ->subject("New Order Received - Order #{$order->order_number}");
            });
            
            \Log::info('Student email sent successfully', ['to' => $student->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send student email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // Create in-app notification
        $this->createInAppNotification($student, [
            'type' => 'order_placed',
            'title' => 'New Order Received',
            'message' => "You have received a new order for {$order->serviceListing->title}",
            'action_url' => route('student.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order confirmation notification to client
     *
     * @param Order $order The order that was confirmed
     * @return void
     */
    public function sendOrderConfirmationNotification(Order $order): void
    {
        \Log::info('sendOrderConfirmationNotification called', ['order_id' => $order->id]);
        
        $client = $order->clientProfile->user;

        \Log::info('Preparing to send client email', [
            'client_email' => $client->email,
            'order_number' => $order->order_number,
        ]);

        try {
            Mail::send('emails.orders.confirmation', [
                'client' => $client,
                'order' => $order,
                'service' => $order->serviceListing,
                'student' => $order->studentProfile->user,
            ], function ($message) use ($client, $order) {
                $message->to($client->email)
                    ->subject("Order Confirmed - Order #{$order->order_number}");
            });
            
            \Log::info('Client email sent successfully', ['to' => $client->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send client email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        $this->createInAppNotification($client, [
            'type' => 'order_confirmed',
            'title' => 'Order Confirmed',
            'message' => "Your order #{$order->order_number} has been confirmed and is in progress",
            'action_url' => route('client.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order accepted notification to client
     *
     * @param Order $order The order that was accepted
     * @return void
     */
    public function sendOrderAcceptedNotification(Order $order): void
    {
        $client = $order->client;

        Mail::send('emails.orders.accepted', [
            'client' => $client,
            'order' => $order,
            'student' => $order->student,
        ], function ($message) use ($client, $order) {
            $message->to($client->email)
                ->subject("Order Accepted - Order #{$order->id}");
        });

        $this->createInAppNotification($client, [
            'type' => 'order_accepted',
            'title' => 'Order Accepted',
            'message' => "Your order has been accepted by {$order->student->name}",
            'action_url' => route('client.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order declined notification to client
     *
     * @param Order $order The order that was declined
     * @return void
     */
    public function sendOrderDeclinedNotification(Order $order): void
    {
        $client = $order->clientProfile->user;
        $reason = $order->decline_reason ?? 'No reason provided';

        Mail::send('emails.orders.declined', [
            'client' => $client,
            'order' => $order,
            'student' => $order->studentProfile->user,
            'reason' => $reason,
        ], function ($message) use ($client, $order) {
            $message->to($client->email)
                ->subject("Order Declined - Order #{$order->order_number}");
        });

        $this->createInAppNotification($client, [
            'type' => 'order_declined',
            'title' => 'Order Declined',
            'message' => "Your order has been declined. Reason: {$reason}",
            'action_url' => route('client.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order completed notification to client
     *
     * @param Order $order The order that was completed
     * @return void
     */
    public function sendOrderCompletedNotification(Order $order): void
    {
        $client = $order->clientProfile->user;

        Mail::send('emails.orders.completed', [
            'client' => $client,
            'order' => $order,
            'student' => $order->studentProfile->user,
        ], function ($message) use ($client, $order) {
            $message->to($client->email)
                ->subject("Order Completed - Order #{$order->order_number}");
        });

        $this->createInAppNotification($client, [
            'type' => 'order_completed',
            'title' => 'Order Completed',
            'message' => "Your order has been completed. Please review and approve.",
            'action_url' => route('client.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order approved notification to student
     *
     * @param Order $order The order that was approved
     * @return void
     */
    public function sendOrderApprovedNotification(Order $order): void
    {
        $student = $order->student;

        Mail::send('emails.orders.approved', [
            'student' => $student,
            'order' => $order,
            'earnings' => $order->total_amount * 0.85, // After 15% platform fee
        ], function ($message) use ($student, $order) {
            $message->to($student->email)
                ->subject("Order Approved - Payment Released");
        });

        $this->createInAppNotification($student, [
            'type' => 'order_approved',
            'title' => 'Order Approved',
            'message' => "Your order has been approved and payment has been released",
            'action_url' => route('student.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send revision requested notification to student
     *
     * @param Order $order The order that needs revision
     * @return void
     */
    public function sendRevisionRequestedNotification(Order $order): void
    {
        $student = $order->studentProfile->user;
        $feedback = $order->revision_notes ?? '';

        Mail::send('emails.orders.revision-requested', [
            'student' => $student,
            'order' => $order,
            'client' => $order->clientProfile->user,
            'feedback' => $feedback,
        ], function ($message) use ($student, $order) {
            $message->to($student->email)
                ->subject("Revision Requested - Order #{$order->order_number}");
        });

        $this->createInAppNotification($student, [
            'type' => 'revision_requested',
            'title' => 'Revision Requested',
            'message' => "Client has requested revisions for order #{$order->order_number}",
            'action_url' => route('student.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send dispute opened notification to both parties and admin
     *
     * @param Order $order The order that has a dispute
     * @return void
     */
    public function sendDisputeOpenedNotification(Order $order): void
    {
        $student = $order->studentProfile->user;
        $client = $order->clientProfile->user;

        // Notify student
        Mail::send('emails.orders.dispute-opened-student', [
            'student' => $student,
            'order' => $order,
            'client' => $client,
        ], function ($message) use ($student, $order) {
            $message->to($student->email)
                ->subject("Dispute Opened - Order #{$order->order_number}");
        });

        // Notify client
        Mail::send('emails.orders.dispute-opened-client', [
            'client' => $client,
            'order' => $order,
            'student' => $student,
        ], function ($message) use ($client, $order) {
            $message->to($client->email)
                ->subject("Dispute Opened - Order #{$order->order_number}");
        });

        $this->createInAppNotification($student, [
            'type' => 'dispute_opened',
            'title' => 'Dispute Opened',
            'message' => "A dispute has been opened for order #{$order->order_number}",
            'action_url' => route('student.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send order cancelled notification
     *
     * @param Order $order The order that was cancelled
     * @return void
     */
    public function sendOrderCancelledNotification(Order $order): void
    {
        $student = $order->studentProfile->user;
        $client = $order->clientProfile->user;

        // Notify both parties
        Mail::send('emails.orders.cancelled', [
            'order' => $order,
            'student' => $student,
            'client' => $client,
            'reason' => $order->cancellation_reason,
        ], function ($message) use ($student, $client, $order) {
            $message->to([$student->email, $client->email])
                ->subject("Order Cancelled - Order #{$order->order_number}");
        });

        $this->createInAppNotification($student, [
            'type' => 'order_cancelled',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} has been cancelled",
            'action_url' => route('student.orders.show', $order),
            'order_id' => $order->id,
        ]);

        $this->createInAppNotification($client, [
            'type' => 'order_cancelled',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} has been cancelled",
            'action_url' => route('client.orders.show', $order),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Send new message notification
     *
     * @param Message $message The message that was sent
     * @return void
     */
    public function sendNewMessageNotification(Message $message): void
    {
        $recipient = $message->recipient;
        $sender = $message->sender;

        Mail::send('emails.messages.new-message', [
            'recipient' => $recipient,
            'sender' => $sender,
            'message' => $message,
        ], function ($mail) use ($recipient, $sender) {
            $mail->to($recipient->email)
                ->subject("New Message from {$sender->name}");
        });

        $this->createInAppNotification($recipient, [
            'type' => 'new_message',
            'title' => 'New Message',
            'message' => "{$sender->name} sent you a message",
            'action_url' => route('messages.show', $message->order_id),
            'message_id' => $message->id,
        ]);
    }

    /**
     * Send review received notification to student
     *
     * @param Review $review The review that was received
     * @return void
     */
    public function sendReviewReceivedNotification(Review $review): void
    {
        $student = $review->student;

        Mail::send('emails.reviews.received', [
            'student' => $student,
            'review' => $review,
            'client' => $review->client,
            'order' => $review->order,
        ], function ($message) use ($student, $review) {
            $message->to($student->email)
                ->subject("New {$review->rating}-Star Review Received");
        });

        $this->createInAppNotification($student, [
            'type' => 'review_received',
            'title' => 'New Review Received',
            'message' => "You received a {$review->rating}-star review",
            'action_url' => route('student.profile.show'),
            'review_id' => $review->id,
        ]);
    }

    /**
     * Send withdrawal processed notification to student
     *
     * @param Withdrawal $withdrawal The withdrawal that was processed
     * @return void
     */
    public function sendWithdrawalProcessedNotification(Withdrawal $withdrawal): void
    {
        $student = $withdrawal->student;

        Mail::send('emails.withdrawals.processed', [
            'student' => $student,
            'withdrawal' => $withdrawal,
        ], function ($message) use ($student, $withdrawal) {
            $message->to($student->email)
                ->subject("Withdrawal Processed - {$withdrawal->formatted_amount}");
        });

        $this->createInAppNotification($student, [
            'type' => 'withdrawal_processed',
            'title' => 'Withdrawal Processed',
            'message' => "Your withdrawal of {$withdrawal->formatted_amount} has been processed",
            'action_url' => route('student.earnings.index'),
            'withdrawal_id' => $withdrawal->id,
        ]);
    }

    /**
     * Send withdrawal failed notification to student
     *
     * @param Withdrawal $withdrawal The withdrawal that failed
     * @param string $reason Reason for failure
     * @return void
     */
    public function sendWithdrawalFailedNotification(Withdrawal $withdrawal, string $reason): void
    {
        $student = $withdrawal->student;

        Mail::send('emails.withdrawals.failed', [
            'student' => $student,
            'withdrawal' => $withdrawal,
            'reason' => $reason,
        ], function ($message) use ($student) {
            $message->to($student->email)
                ->subject("Withdrawal Failed");
        });

        $this->createInAppNotification($student, [
            'type' => 'withdrawal_failed',
            'title' => 'Withdrawal Failed',
            'message' => "Your withdrawal failed. Reason: {$reason}",
            'action_url' => route('student.earnings.index'),
            'withdrawal_id' => $withdrawal->id,
        ]);
    }

    /**
     * Send withdrawal notification based on status
     *
     * @param Withdrawal $withdrawal The withdrawal
     * @param string $status The withdrawal status
     * @return void
     */
    public function sendWithdrawalNotification(Withdrawal $withdrawal, string $status): void
    {
        if ($status === 'completed') {
            $this->sendWithdrawalProcessedNotification($withdrawal);
        } elseif ($status === 'failed') {
            $reason = $withdrawal->notes ?? 'Unknown error';
            $this->sendWithdrawalFailedNotification($withdrawal, $reason);
        }
    }

    /**
     * Create an in-app notification
     *
     * @param User $user The user to notify
     * @param array $data Notification data
     * @return void
     */
    protected function createInAppNotification(User $user, array $data): void
    {
        // This would typically use Laravel's notification system
        // For now, we'll store it in a notifications table or user metadata
        // Implementation depends on your notification storage strategy
        
        // Example using Laravel notifications:
        // $user->notify(new \App\Notifications\GenericNotification($data));
    }
}
