<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 28px; color: #1e40af; }
        .header p { margin: 5px 0; color: #6b7280; font-size: 12px; }
        .invoice-info { display: table; width: 100%; margin-bottom: 20px; }
        .invoice-info .left, .invoice-info .right { display: table-cell; width: 50%; vertical-align: top; }
        .invoice-info .right { text-align: right; }
        .label { font-weight: bold; color: #374151; }
        .section-title { font-size: 16px; font-weight: bold; color: #1e40af; margin: 20px 0 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #2563eb; color: #fff; padding: 10px 8px; text-align: left; font-size: 13px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 10px 8px; font-size: 13px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .totals { width: 300px; margin-left: auto; margin-top: 20px; }
        .totals table { margin: 0; }
        .totals td { border: none; padding: 6px 8px; }
        .totals .grand-total td { font-size: 18px; font-weight: bold; color: #1e40af; border-top: 2px solid #2563eb; padding-top: 12px; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE</h1>
        <p>Order #{{ $order->order_number }}</p>
    </div>

    <div class="invoice-info">
        <div class="left">
            <p class="section-title">Customer Details</p>
            <p><span class="label">Name:</span> {{ $order->name }}</p>
            <p><span class="label">Phone:</span> {{ $order->phone }}</p>
            @if($order->email)
            <p><span class="label">Email:</span> {{ $order->email }}</p>
            @endif
            <p><span class="label">Address:</span> {{ $order->address_line }}, {{ $order->area }}, {{ $order->city }}</p>
        </div>
        <div class="right">
            <p class="section-title">Invoice Details</p>
            <p><span class="label">Date:</span> {{ $order->created_at->format('d M Y') }}</p>
            <p><span class="label">Status:</span> {{ ucfirst($order->status) }}</p>
            <p><span class="label">Zone:</span> {{ $order->shipping_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</p>
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
                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align:right">৳{{ number_format($item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td style="text-align:right">৳{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td style="text-align:right; color: #16a34a;">-৳{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Shipping:</td>
                <td style="text-align:right">৳{{ number_format($order->shipping_fee, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td style="text-align:right">৳{{ number_format($order->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>This is a computer generated invoice.</p>
    </div>

</body>
</html>
