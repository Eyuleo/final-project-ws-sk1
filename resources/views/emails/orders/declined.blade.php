<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #EF4444; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .reason-box { background-color: #FEE2E2; padding: 15px; margin: 15px 0; border-left: 4px solid #EF4444; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Declined</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $client->name }},</p>
            
            <p>We regret to inform you that your order has been declined by {{ $student->name }}.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Provider:</strong> {{ $student->name }}</p>
                <p><strong>Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Status:</strong> <span style="color: #EF4444;">Declined</span></p>
            </div>
            
            @if(isset($reason) && $reason)
            <div class="reason-box">
                <h4 style="margin-top: 0;">Reason for Decline:</h4>
                <p style="margin-bottom: 0;">{{ $reason }}</p>
            </div>
            @endif
            
            <p>Your payment has been fully refunded and will appear in your account within 5-10 business days.</p>
            
            <p>We encourage you to explore other service providers who may be able to assist you with your requirements.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('client.services.index') }}" class="button">Browse Services</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
