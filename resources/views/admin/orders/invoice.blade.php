<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #2563eb; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #1e40af; }
        .header p { margin: 5px 0; color: #6b7280; font-size: 10px; }
        .invoice-info { display: table; width: 100%; margin-bottom: 15px; }
        .invoice-info .left, .invoice-info .right { display: table-cell; width: 50%; vertical-align: top; }
        .invoice-info .right { text-align: right; }
        .label { font-weight: bold; color: #374151; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e40af; margin: 15px 0 5px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #2563eb; color: #fff; padding: 8px 6px; text-align: left; font-size: 11px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 8px 6px; font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .totals { width: 250px; margin-left: auto; margin-top: 15px; }
        .totals table { margin: 0; }
        .totals td { border: none; padding: 4px 6px; }
        .totals .grand-total td { font-size: 14px; font-weight: bold; color: #1e40af; border-top: 2px solid #2563eb; padding-top: 8px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>

@php
    $siteName = \App\Models\Setting::get('site_name', 'Perfume Store');
    $siteLogo = \App\Models\Setting::get('site_logo');
    $supportPhone = \App\Models\Setting::get('support_phone', '');
    $supportEmail = \App\Models\Setting::get('support_email', '');
    $companyAddress = \App\Models\Setting::get('company_address', '');
    
    // We base64 encode the image so DOMPDF can parse it easily without relying on network URLs
    // Though public_path is preferred for DOMPDF if chroot is configured correctly
    $logoPath = $siteLogo ? public_path('storage/' . $siteLogo) : null;
@endphp

    <div class="header">
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="border: none; padding: 0; text-align: left; vertical-align: top;">
                    @if($logoPath && file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo" style="max-height: 60px; max-width: 200px;">
                    @else
                        <h2 style="color: #1e40af; margin: 0; font-size: 24px;">{{ $siteName }}</h2>
                    @endif
                    
                    <div style="margin-top: 10px; color: #4b5563; font-size: 12px; line-height: 1.4;">
                        @if($companyAddress)<div>{!! nl2br(e($companyAddress)) !!}</div>@endif
                        @if($supportPhone)<div>Phone: {{ $supportPhone }}</div>@endif
                        @if($supportEmail)<div>Email: {{ $supportEmail }}</div>@endif
                    </div>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                    <h1 style="color: #1e40af; margin: 0; font-size: 24px; font-weight: bold;">INVOICE</h1>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 12px;">Order #{{ $order->order_number }}</p>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 12px;">Date: {{ $order->created_at->format('d M Y') }}</p>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 12px;">Status: <strong>{{ ucfirst($order->status) }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-info">
        <div class="left">
            <p class="section-title">Bill To</p>
            <p><span class="label">Name:</span> {{ $order->name }}</p>
            <p><span class="label">Phone:</span> {{ $order->phone }}</p>
            @if($order->email)
            <p><span class="label">Email:</span> {{ $order->email }}</p>
            @endif
            <p><span class="label">Address:</span> {{ $order->address_line }}, {{ $order->area }}, {{ $order->city }}</p>
        </div>
        <div class="right">
            <p class="section-title">Shipping Details</p>
            <p><span class="label">Shipping Zone:</span> {{ $order->shipping_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</p>
            <p><span class="label">Payment Method:</span> {{ $order->payment_method ?? 'Cash on Delivery' }}</p>
            @if($order->payment_status === 'paid' || $order->payment_status === 'pending_verification')
            <p><span class="label">Payment Status:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</p>
            @endif
        </div>
    </div>

    <p class="section-title">Order Items</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Variant</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name_snapshot }}</td>
                <td>{{ $item->ml_value }}{{ $item->ml_unit }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align:right">{{ number_format($item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td style="text-align:right">{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td style="text-align:right; color: #16a34a;">-{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Shipping (Advance):</td>
                <td style="text-align:right">{{ number_format($order->shipping_fee, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td style="text-align:right">{{ number_format($order->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td class="label" style="padding-top: 15px; font-size: 14px; color: #b91c1c;">Payable Amount (COD):</td>
                <td style="text-align:right; padding-top: 15px; font-size: 14px; font-weight: bold; color: #b91c1c;">
                    {{ number_format($order->grand_total - $order->shipping_fee, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div style="margin-bottom: 20px; padding: 10px; background-color: #fef08a; border-left: 4px solid #ca8a04; color: #854d0e; text-align: left; font-size: 10px; font-weight: bold;">
            Note: The delivery charge must be paid in advance to confirm the order. The "Payable Amount (COD)" shown above is to be paid upon delivery.
        </div>
        <p>Thank you for your purchase!</p>
        <p>This is a computer generated invoice.</p>
    </div>

</body>
</html>
