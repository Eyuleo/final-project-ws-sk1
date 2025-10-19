<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\EscrowService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected EscrowService $escrowService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        \Log::info('=== WEBHOOK RECEIVED ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
        
        // Disable Telescope for webhooks to prevent database errors
        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            \Laravel\Telescope\Telescope::stopRecording();
        }
        
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        
        \Log::info('Webhook payload received', [
            'has_signature' => !empty($signature),
            'payload_length' => strlen($payload),
        ]);

        try {
            $event = $this->paymentService->verifyWebhookSignature($payload, $signature);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // Handle the event
        try {
            \Log::info('Processing webhook event', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);
            
            switch ($event->type) {
                case 'checkout.session.completed':
                    \Log::info('Handling checkout.session.completed');
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    \Log::info('Finished handling checkout.session.completed');
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'type' => $event->type,
                        'id' => $event->id,
                    ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error processing Stripe webhook', [
                'event_type' => $event->type ?? 'unknown',
                'event_id' => $event->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Stripe from retrying
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Handle checkout.session.completed event
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'payment_status' => $session->payment_status,
        ]);

        if ($session->payment_status === 'paid') {
            Log::info('Processing paid checkout session', [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
            ]);
            
            $this->paymentService->handleSuccessfulPayment($session);
            
            // Hold funds in escrow
            $orderId = $session->metadata->order_id ?? $session->client_reference_id;
            Log::info('Extracted order ID from session', ['order_id' => $orderId]);
            
            if ($orderId) {
                $order = \App\Models\Order::find($orderId);
                if ($order) {
                    Log::info('Order found, processing escrow and notifications', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ]);
                    
                    $this->escrowService->holdFunds($order);
                    
                    // Send notifications to both client and student
                    try {
                        Log::info('Sending student notification');
                        $this->notificationService->sendOrderPlacedNotification($order);
                        Log::info('Student notification sent successfully');
                        
                        Log::info('Sending client notification');
                        $this->notificationService->sendOrderConfirmationNotification($order);
                        Log::info('Client notification sent successfully');
                    } catch (\Exception $e) {
                        Log::error('Failed to send notifications', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    
                    Log::info('Funds held in escrow and notifications sent', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'amount' => $order->total_amount,
                    ]);
                } else {
                    Log::warning('Order not found', ['order_id' => $orderId]);
                }
            } else {
                Log::warning('No order ID found in session metadata');
            }
        }
    }

    /**
     * Handle payment_intent.succeeded event
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount / 100,
        ]);

        try {
            $this->paymentService->handlePaymentSuccess($paymentIntent->id);
        } catch (\Exception $e) {
            Log::warning('Could not process payment success', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle payment_intent.payment_failed event
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'failure_message' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
        ]);

        $this->paymentService->handlePaymentFailed($paymentIntent->id);
    }

    /**
     * Handle charge.refunded event
     */
    protected function handleChargeRefunded($charge)
    {
        Log::info('Charge refunded', [
            'charge_id' => $charge->id,
            'amount_refunded' => $charge->amount_refunded / 100,
        ]);

        // Additional refund processing if needed
        // The refund transaction is already created by PaymentService::processRefund
    }
}
