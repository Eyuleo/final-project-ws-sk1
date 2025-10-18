<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Refund;
use Stripe\Stripe;

/**
 * PaymentService handles all Stripe payment operations including:
 * - Checkout session creation for order payments
 * - Payment intent management
 * - Refund processing
 * - Webhook signature verification
 */
class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    /**
     * Create a Stripe Checkout session for an order payment
     *
     * @param Order $order The order to create payment for
     * @return Session The created Stripe Checkout session
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Order $order): Session
    {
        $successUrl = route('client.orders.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('client.orders.cancel');

        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $order->serviceListing->title,
                        'description' => "Order #{$order->order_number} - {$order->serviceListing->title}",
                    ],
                    'unit_amount' => (int) ($order->total_amount * 100), // Convert to cents
                ],
                'quantity' => 1,
            ],
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $order->id,
            'customer_email' => $order->clientProfile->user->email,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'student_profile_id' => $order->student_profile_id,
                'client_profile_id' => $order->client_profile_id,
            ],
        ]);

        // Store session ID on order
        $order->update(['stripe_session_id' => $session->id]);

        return $session;
    }

    /**
     * Process a refund for an order
     *
     * @param Order $order The order to refund
     * @param float|null $amount Amount to refund (null for full refund)
     * @param string|null $reason Reason for the refund
     * @return Refund The created Stripe refund
     * @throws ApiErrorException
     */
    public function processRefund(Order $order, ?float $amount = null, ?string $reason = null): Refund
    {
        $paymentTransaction = $order->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->first();

        if (!$paymentTransaction || !$paymentTransaction->stripe_payment_intent_id) {
            throw new \RuntimeException('No valid payment found for this order');
        }

        $refundData = [
            'payment_intent' => $paymentTransaction->stripe_payment_intent_id,
            'metadata' => [
                'order_id' => $order->id,
                'refund_reason' => $reason ?? 'Order refund',
            ],
        ];

        if ($amount !== null) {
            $refundData['amount'] = (int) ($amount * 100); // Convert to cents
        }

        $refund = Refund::create($refundData);

        // Create refund transaction record
        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $order->client_id,
            'type' => 'refund',
            'amount' => $amount ?? $order->total_amount,
            'status' => 'completed',
            'stripe_payment_intent_id' => $paymentTransaction->stripe_payment_intent_id,
            'stripe_refund_id' => $refund->id,
            'metadata' => [
                'reason' => $reason,
                'refund_id' => $refund->id,
            ],
        ]);

        return $refund;
    }

    /**
     * Verify Stripe webhook signature
     *
     * @param string $payload The webhook payload
     * @param string $signature The Stripe signature header
     * @return \Stripe\Event The verified Stripe event
     * @throws \UnexpectedValueException|\Stripe\Exception\SignatureVerificationException
     */
    public function verifyWebhookSignature(string $payload, string $signature): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('stripe.webhook_secret')
        );
    }

    /**
     * Handle successful payment webhook
     *
     * @param string $paymentIntentId
     * @return void
     * @throws \Exception
     */
    public function handlePaymentSuccess(string $paymentIntentId): void
    {
        // Find order by payment intent
        $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntentId)
            ->where('type', 'payment')
            ->first();

        if (!$transaction) {
            throw new \Exception("Transaction not found for payment intent: {$paymentIntentId}");
        }

        $order = $transaction->order;

        // Update transaction status
        $transaction->update(['status' => 'completed']);

        // Update order status to pending (awaiting student acceptance)
        $order->update([
            'status' => 'pending',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Handle failed payment webhook
     *
     * @param string $paymentIntentId
     * @return void
     */
    public function handlePaymentFailed(string $paymentIntentId): void
    {
        $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntentId)
            ->where('type', 'payment')
            ->first();

        if ($transaction) {
            $transaction->update(['status' => 'failed']);
            
            $order = $transaction->order;
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);
        }
    }

    /**
     * Handle successful payment from webhook (session-based)
     *
     * @param Session $session The Stripe Checkout session
     * @return void
     */
    public function handleSuccessfulPayment(Session $session): void
    {
        $orderId = $session->metadata->order_id ?? $session->client_reference_id;
        $order = Order::findOrFail($orderId);

        // Create payment transaction record
        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $order->clientProfile->user_id,
            'type' => 'payment',
            'amount' => $order->total_amount,
            'status' => 'completed',
            'stripe_payment_intent_id' => $session->payment_intent,
            'metadata' => [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
            ],
        ]);

        // Update order payment status to pending (awaiting student acceptance)
        $order->update([
            'status' => 'pending',
            'payment_status' => 'paid',
            'stripe_session_id' => $session->id,
        ]);
    }

    /**
     * Create or retrieve Stripe customer for client
     *
     * @param \App\Models\ClientProfile $client
     * @return string Stripe customer ID
     * @throws ApiErrorException
     */
    public function getOrCreateStripeCustomer(\App\Models\ClientProfile $client): string
    {
        if ($client->stripe_customer_id) {
            return $client->stripe_customer_id;
        }

        $customer = \Stripe\Customer::create([
            'email' => $client->user->email,
            'name' => $client->user->name,
            'metadata' => [
                'client_profile_id' => $client->id,
                'user_id' => $client->user_id,
            ],
        ]);

        $client->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    /**
     * Calculate platform fee (15% commission)
     *
     * @param float $amount The order amount
     * @return float The platform fee
     */
    public function calculatePlatformFee(float $amount): float
    {
        return round($amount * 0.15, 2);
    }

    /**
     * Calculate student earnings after platform fee
     *
     * @param float $amount The order amount
     * @return float The student earnings
     */
    public function calculateStudentEarnings(float $amount): float
    {
        return round($amount * 0.85, 2);
    }
}
