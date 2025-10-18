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
     * @param string $successUrl URL to redirect after successful payment
     * @param string $cancelUrl URL to redirect if payment is cancelled
     * @return Session The created Stripe Checkout session
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Order $order, string $successUrl, string $cancelUrl): Session
    {
        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $order->serviceListing->title,
                        'description' => "Order #{$order->id} - {$order->serviceListing->title}",
                    ],
                    'unit_amount' => (int) ($order->total_amount * 100), // Convert to cents
                ],
                'quantity' => 1,
            ],
        ];

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $order->id,
            'customer_email' => $order->client->email,
            'metadata' => [
                'order_id' => $order->id,
                'student_id' => $order->student_id,
                'client_id' => $order->client_id,
            ],
        ]);
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
     * Handle successful payment from webhook
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
            'user_id' => $order->client_id,
            'type' => 'payment',
            'amount' => $order->total_amount,
            'status' => 'completed',
            'stripe_payment_intent_id' => $session->payment_intent,
            'metadata' => [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status,
            ],
        ]);

        // Update order payment status
        $order->update([
            'payment_status' => 'paid',
            'stripe_session_id' => $session->id,
        ]);
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
