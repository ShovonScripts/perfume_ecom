<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #d4af37; } /* Gold color for luxury feel */
        .content { margin-bottom: 20px; }
        .order-details { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .order-details th { text-align: left; padding-right: 15px; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; background-color: #000; color: #fff;}
        .footer { text-align: center; font-size: 0.9em; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->name }},</p>
            <p>The status of your order <strong>#{{ $order->order_number }}</strong> has been updated.</p>
            
            <p>New Status: <span class="status-badge">{{ ucfirst($order->status) }}</span></p>

            <div class="order-details">
                <h3>Order Summary</h3>
                <table style="width: 100%;">
                    <tr>
                        <th>Order Date:</th>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount:</th>
                        <td>৳ {{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>

            <p>You can track your order or view the full details by logging into your account or contacting our support.</p>
            <p>Thank you for shopping with us!</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
