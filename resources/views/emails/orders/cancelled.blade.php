<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #6B7280; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .info-box { background-color: #F3F4F6; padding: 15px; margin: 15px 0; border-left: 4px solid #6B7280; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Cancelled</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p>This is to inform you that the following order has been cancelled:</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Client:</strong> {{ $client->name }}</p>
                <p><strong>Provider:</strong> {{ $student->name }}</p>
                <p><strong>Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Status:</strong> <span style="color: #6B7280;">Cancelled</span></p>
                <p><strong>Cancelled On:</strong> {{ now()->format('F d, Y h:i A') }}</p>
            </div>
            
            @if($order->cancellation_reason)
            <div class="info-box">
                <h4 style="margin-top: 0;">Cancellation Reason:</h4>
                <p style="margin-bottom: 0;">{{ $order->cancellation_reason }}</p>
            </div>
            @endif
            
            <div class="info-box">
                <h4 style="margin-top: 0;">Refund Information</h4>
                <p style="margin-bottom: 0;">
                    @if($order->status === 'pending' || $order->status === 'pending_payment')
                        A full refund has been processed and will appear in the client's account within 5-10 business days.
                    @else
                        The refund amount will be determined based on the work completed and our refund policy.
                    @endif
                </p>
            </div>
            
            <p>If you have any questions or concerns about this cancellation, please contact our support team.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('client.services.index') }}" class="button">Browse Other Services</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
