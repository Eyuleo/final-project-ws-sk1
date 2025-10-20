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
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #10B981; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .button-secondary { background-color: #6B7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Deliverables Ready!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $client->name }},</p>
            
            <p>{{ $student->name }} has completed your order and uploaded the deliverables for your review.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Provider:</strong> {{ $student->name }}</p>
                <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Completed On:</strong> {{ $order->updated_at->format('F d, Y h:i A') }}</p>
                <p><strong>Status:</strong> <span style="color: #10B981;">Delivered - Awaiting Your Review</span></p>
            </div>
            
            @if($order->delivery_note)
            <div style="background-color: #EFF6FF; padding: 15px; margin: 15px 0; border-left: 4px solid #3B82F6; border-radius: 5px;">
                <h4 style="margin-top: 0;">Delivery Note:</h4>
                <p style="margin-bottom: 0;">{{ $order->delivery_note }}</p>
            </div>
            @endif
            
            <p><strong>What's next?</strong></p>
            <ul>
                <li>Review the deliverables on your order page</li>
                <li>If satisfied, approve the order to release payment</li>
                <li>If changes are needed, request a revision ({{ $order->revision_count }}/{{ $order->max_revisions }} revisions used)</li>
            </ul>
            
            <p>Please note: Payment is held in escrow and will only be released to the provider once you approve the order or after 7 days of delivery.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('client.orders.show', $order) }}" class="button">Review Deliverables</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
