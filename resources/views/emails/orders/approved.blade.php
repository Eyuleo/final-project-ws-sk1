<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #10B981; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .earnings-box { background-color: #D1FAE5; padding: 15px; margin: 15px 0; border-left: 4px solid #10B981; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #10B981; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Order Approved!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $student->name }},</p>
            
            <p>Congratulations! Your client has approved the order and is satisfied with your work.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Client:</strong> {{ $order->clientProfile->user->name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Approved On:</strong> {{ now()->format('F d, Y h:i A') }}</p>
                <p><strong>Status:</strong> <span style="color: #10B981;">Approved & Completed</span></p>
            </div>
            
            <div class="earnings-box">
                <h4 style="margin-top: 0;">💰 Your Earnings</h4>
                <p style="font-size: 18px; margin: 10px 0;">
                    <strong style="color: #10B981; font-size: 24px;">${{ number_format($earnings, 2) }}</strong>
                    <small style="color: #6B7280; display: block; margin-top: 5px;">
                        (after 15% platform fee)
                    </small>
                </p>
                <p style="margin-bottom: 0; font-size: 14px;">
                    Your earnings have been released from escrow and added to your available balance. You can now withdraw funds to your bank account.
                </p>
            </div>
            
            <p>The client may also leave a review for your service. Great reviews help you attract more clients!</p>
            
            <p style="text-align: center;">
                <a href="{{ route('student.earnings.index') }}" class="button">View Earnings</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
