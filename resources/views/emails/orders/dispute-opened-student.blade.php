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
        .warning-box { background-color: #FEE2E2; padding: 15px; margin: 15px 0; border-left: 4px solid #DC2626; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #DC2626; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Dispute Opened</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $student->name }},</p>
            
            <p>A dispute has been opened by {{ $client->name }} for the following order:</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Client:</strong> {{ $client->name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Status:</strong> <span style="color: #DC2626;">Disputed</span></p>
            </div>
            
            @if($order->dispute_reason)
            <div class="warning-box">
                <h4 style="margin-top: 0;">Dispute Reason:</h4>
                <p style="margin-bottom: 0;">{{ $order->dispute_reason }}</p>
            </div>
            @endif
            
            <div class="warning-box">
                <h4 style="margin-top: 0;">⚠️ Important Information</h4>
                <p>Our admin team will review this dispute and work with both parties to reach a fair resolution. The payment for this order is currently held in escrow.</p>
                <p style="margin-bottom: 0;"><strong>Please respond within 48 hours to avoid automatic resolution against you.</strong></p>
            </div>
            
            <p><strong>What you should do:</strong></p>
            <ul>
                <li>Review the dispute reason carefully</li>
                <li>Gather any evidence or documentation</li>
                <li>Respond to the dispute through the order page</li>
                <li>Communicate professionally with our admin team</li>
            </ul>
            
            <p style="text-align: center;">
                <a href="{{ route('student.orders.show', $order) }}" class="button">View Dispute & Respond</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
