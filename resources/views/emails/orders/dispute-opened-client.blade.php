<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #DC2626; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .info-box { background-color: #DBEAFE; padding: 15px; margin: 15px 0; border-left: 4px solid #3B82F6; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #DC2626; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dispute Submitted</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $client->name }},</p>
            
            <p>Your dispute has been successfully submitted for the following order:</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Provider:</strong> {{ $student->name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Status:</strong> <span style="color: #DC2626;">Disputed</span></p>
            </div>
            
            @if($order->dispute_reason)
            <div class="info-box">
                <h4 style="margin-top: 0;">Your Dispute Reason:</h4>
                <p style="margin-bottom: 0;">{{ $order->dispute_reason }}</p>
            </div>
            @endif
            
            <div class="info-box">
                <h4 style="margin-top: 0;">What Happens Next?</h4>
                <p>Our admin team will review your dispute within 24-48 hours. They will:</p>
                <ul style="margin: 10px 0;">
                    <li>Review all evidence and communication</li>
                    <li>Contact both parties if needed</li>
                    <li>Make a fair decision based on our policies</li>
                </ul>
                <p style="margin-bottom: 0;">Your payment is currently held in escrow and will be handled according to the resolution.</p>
            </div>
            
            <p>You may be contacted by our admin team for additional information. Please check your email regularly and respond promptly.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('client.orders.show', $order) }}" class="button">View Order Status</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
