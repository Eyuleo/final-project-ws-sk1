<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .order-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .button { display: inline-block; padding: 12px 24px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order Received!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $student->name }},</p>
            
            <p>You have received a new order for your service!</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Service:</strong> {{ $order->serviceListing->title }}</p>
                <p><strong>Client:</strong> {{ $client->name }}</p>
                <p><strong>Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Your Earnings:</strong> ${{ number_format($order->total_amount * 0.85, 2) }} (after 15% platform fee)</p>
                <p><strong>Deadline:</strong> {{ $order->deadline->format('F d, Y') }}</p>
                
                <h4>Requirements:</h4>
                <p>{{ $order->requirements }}</p>
            </div>
            
            <p>Please review the order details and accept or decline within 24 hours.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('student.orders.show', $order) }}" class="button">View Order</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
