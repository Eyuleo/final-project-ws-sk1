<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable and secret keys from your Stripe account.
    | You can find these in your Stripe Dashboard.
    |
    */

    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The webhook secret is used to verify that webhook events are sent
    | by Stripe and not by a third party.
    |
    */

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Connect Settings
    |--------------------------------------------------------------------------
    |
    | Settings for Stripe Connect used for student payouts.
    |
    */

    'connect' => [
        'client_id' => env('STRIPE_CONNECT_CLIENT_ID'),
        'redirect_uri' => env('APP_URL') . '/stripe/connect/callback',
    ],

    /*
    |--------------------------------------------------------------------------
    | Platform Fee Percentage
    |--------------------------------------------------------------------------
    |
    | The percentage fee charged by the platform on each transaction.
    | Default is 15% as specified in the business requirements.
    |
    */

    'platform_fee_percentage' => env('STRIPE_PLATFORM_FEE', 15),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for all transactions.
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | The payment methods to enable in Stripe Checkout.
    |
    */

    'payment_methods' => [
        'card',
    ],

    /*
    |--------------------------------------------------------------------------
    | Escrow Auto-Release Days
    |--------------------------------------------------------------------------
    |
    | Number of days after delivery before funds are automatically released
    | from escrow to the student if client doesn't approve/reject.
    |
    */

    'escrow_auto_release_days' => env('STRIPE_ESCROW_AUTO_RELEASE_DAYS', 7),

];
