<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); padding:40px 40px 30px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700; letter-spacing:-0.5px;">
                                ✨ Order Confirmed!
                            </h1>
                            <p style="margin:8px 0 0; color:#94a3b8; font-size:14px;">
                                Thank you for your purchase
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding:30px 40px 20px;">
                            <p style="margin:0; font-size:16px; color:#1e293b;">
                                Hi <strong>{{ $order->name }}</strong>,
                            </p>
                            <p style="margin:10px 0 0; font-size:14px; color:#64748b; line-height:1.6;">
                                Your order has been placed successfully! Here's your order summary:
                            </p>
                        </td>
                    </tr>

                    <!-- Order Number Badge -->
                    <tr>
                        <td style="padding:0 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:#eff6ff; border: 1px solid #bfdbfe; border-radius:12px; padding:16px 20px; text-align:center;">
                                        <span style="font-size:12px; color:#3b82f6; text-transform:uppercase; letter-spacing:1px; font-weight:600;">
                                            Order Number
                                        </span>
                                        <br>
                                        <span style="font-size:20px; font-weight:700; color:#1e40af; letter-spacing:1px;">
                                            {{ $order->order_number }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Items Header -->
                    <tr>
                        <td style="padding:0 40px 10px;">
                            <h3 style="margin:0; font-size:14px; color:#1e293b; text-transform:uppercase; letter-spacing:1px; font-weight:600;">
                                Order Items
                            </h3>
                        </td>
                    </tr>

                    <!-- Order Items -->
                    <tr>
                        <td style="padding:0 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                                <!-- Table Header -->
                                <tr>
                                    <td style="background-color:#f8fafc; padding:12px 16px; font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">
                                        Product
                                    </td>
                                    <td style="background-color:#f8fafc; padding:12px 16px; font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; border-bottom:1px solid #e2e8f0; text-align:center;">
                                        Qty
                                    </td>
                                    <td style="background-color:#f8fafc; padding:12px 16px; font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; border-bottom:1px solid #e2e8f0; text-align:right;">
                                        Total
                                    </td>
                                </tr>
                                <!-- Items -->
                                @foreach($order->items as $item)
                                <tr>
                                    <td style="padding:14px 16px; border-bottom:1px solid #f1f5f9;">
                                        <span style="font-size:14px; color:#1e293b; font-weight:600;">{{ $item->name_snapshot }}</span>
                                        <br>
                                        <span style="font-size:12px; color:#94a3b8;">{{ $item->ml_value }}{{ $item->ml_unit }} · ৳{{ number_format($item->unit_price) }} each</span>
                                    </td>
                                    <td style="padding:14px 16px; border-bottom:1px solid #f1f5f9; text-align:center; font-size:14px; color:#475569;">
                                        ×{{ $item->quantity }}
                                    </td>
                                    <td style="padding:14px 16px; border-bottom:1px solid #f1f5f9; text-align:right; font-size:14px; font-weight:700; color:#1e293b;">
                                        ৳{{ number_format($item->line_total) }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <!-- Totals -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                                <tr>
                                    <td style="padding:12px 16px; font-size:14px; color:#64748b; border-bottom:1px solid #f1f5f9;">Subtotal</td>
                                    <td style="padding:12px 16px; font-size:14px; color:#1e293b; text-align:right; border-bottom:1px solid #f1f5f9;">৳{{ number_format($order->subtotal) }}</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <td style="padding:12px 16px; font-size:14px; color:#16a34a; border-bottom:1px solid #f1f5f9;">Discount</td>
                                    <td style="padding:12px 16px; font-size:14px; color:#16a34a; text-align:right; border-bottom:1px solid #f1f5f9;">-৳{{ number_format($order->discount) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 16px; font-size:14px; color:#64748b; border-bottom:1px solid #f1f5f9;">
                                        Shipping
                                        <span style="font-size:12px; color:#94a3b8;">({{ $order->shipping_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }})</span>
                                    </td>
                                    <td style="padding:12px 16px; font-size:14px; color:#1e293b; text-align:right; border-bottom:1px solid #f1f5f9;">৳{{ number_format($order->shipping_fee) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:16px; font-size:18px; font-weight:700; color:#1e293b; background-color:#f0fdf4;">Grand Total</td>
                                    <td style="padding:16px; font-size:18px; font-weight:700; color:#16a34a; text-align:right; background-color:#f0fdf4;">৳{{ number_format($order->grand_total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Delivery Info -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fefce8; border:1px solid #fde68a; border-radius:12px; padding:20px;">
                                <tr>
                                    <td style="padding:20px;">
                                        <h3 style="margin:0 0 10px; font-size:14px; color:#92400e; font-weight:700;">
                                            📍 Delivery Address
                                        </h3>
                                        <p style="margin:0; font-size:14px; color:#78350f; line-height:1.6;">
                                            {{ $order->name }}<br>
                                            {{ $order->phone }}<br>
                                            {{ $order->address_line }}<br>
                                            {{ $order->area }}, {{ $order->city }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA -->
                    <tr>
                        <td style="padding:0 40px 30px; text-align:center;">
                            <p style="margin:0 0 15px; font-size:14px; color:#64748b;">
                                We'll contact you at <strong>{{ $order->phone }}</strong> for delivery confirmation.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc; padding:24px 40px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:13px; color:#94a3b8;">
                                Thank you for shopping with us! 💜
                            </p>
                            <p style="margin:8px 0 0; font-size:11px; color:#cbd5e1;">
                                This is an automated email. Please do not reply directly.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
