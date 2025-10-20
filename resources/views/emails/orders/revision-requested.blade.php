<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #F59E0B; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .feedback-box { background-color: #FEF3C7; padding: 15px; margin: 15px 0; border-left: 4px solid #F59E0B; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #F59E0B; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Revision Requested</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $student->name }},</p>
            
            <p>Your client {{ $client->name }} has requested a revision for the following order:</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Client:</strong> {{ $client->name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Revisions Used:</strong> {{ $order->revision_count }}/{{ $order->max_revisions }}</p>
                <p><strong>Status:</strong> <span style="color: #F59E0B;">Revision Requested</span></p>
            </div>
            
            @if($feedback)
            <div class="feedback-box">
                <h4 style="margin-top: 0;">Client Feedback:</h4>
                <p style="margin-bottom: 0;">{{ $feedback }}</p>
            </div>
            @endif
            
            <p><strong>What's next?</strong></p>
            <ul>
                <li>Review the client's feedback carefully</li>
                <li>Make the necessary changes to your work</li>
                <li>Upload the revised deliverables</li>
            </ul>
            
            @if($order->revision_count >= $order->max_revisions)
            <p style="color: #DC2626;"><strong>Note:</strong> This is the final revision for this order. Please ensure all requirements are met.</p>
            @endif
            
            <p style="text-align: center;">
                <a href="{{ route('student.orders.show', $order) }}" class="button">View Order & Upload Revision</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
