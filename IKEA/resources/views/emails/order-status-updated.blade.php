<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Update — IKEA Philippines</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f0;
            color: #111;
            padding: 32px 16px;
        }

        .wrapper { max-width: 600px; margin: 0 auto; }

        .email-header {
            border-radius: 10px 10px 0 0;
            padding: 32px;
            text-align: center;
        }

        /* Header colour changes per status */
        .email-header.pending    { background: #f57c00; }
        .email-header.processing { background: #0058A3; }
        .email-header.completed  { background: #2e7d32; }
        .email-header.cancelled  { background: #CC0008; }

        .logo-box {
            display: inline-block;
            background: #FFDB00;
            color: #0058A3;
            font-weight: 900;
            font-size: 28px;
            letter-spacing: 2px;
            padding: 6px 18px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .status-icon { font-size: 48px; display: block; margin-bottom: 12px; }
        .email-header h1 { color: white; font-size: 24px; font-weight: 800; }
        .email-header p  { color: rgba(255,255,255,0.85); font-size: 15px; margin-top: 8px; }

        .email-body { background: white; padding: 32px; }

        .order-meta {
            display: flex;
            justify-content: space-between;
            background: #f5f5f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .meta-item { text-align: center; }
        .meta-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #767676; }
        .meta-value { font-size: 15px; font-weight: 800; color: #111; margin-top: 3px; }

        /* Status badge */
        .status-badge {
            display: inline-block;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 18px;
            border-radius: 40px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge.pending    { background: #fff3e0; color: #f57c00; }
        .status-badge.processing { background: #e3f2fd; color: #1565c0; }
        .status-badge.completed  { background: #e8f5e9; color: #2e7d32; }
        .status-badge.cancelled  { background: #ffebee; color: #CC0008; }

        .message-box {
            border-left: 4px solid #0058A3;
            background: #f5f9ff;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
            font-size: 14px;
            line-height: 1.7;
            color: #333;
        }
        .message-box.cancelled { border-left-color: #CC0008; background: #fff8f8; }
        .message-box.completed { border-left-color: #2e7d32; background: #f5fff7; }

        .section-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #767676;
            border-bottom: 2px solid #e5e5e5;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .product-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .product-row:last-child { border-bottom: none; }
        .product-name { font-weight: 700; }
        .product-qty  { color: #767676; font-size: 13px; }
        .product-price { font-weight: 800; }

        .cta-wrap { text-align: center; margin: 28px 0; }
        .cta-btn {
            display: inline-block;
            background: #FFDB00;
            color: #111;
            font-weight: 800;
            font-size: 15px;
            padding: 14px 36px;
            border-radius: 40px;
            text-decoration: none;
        }

        .email-footer {
            background: #111;
            border-radius: 0 0 10px 10px;
            padding: 24px 32px;
            text-align: center;
        }
        .email-footer p { color: rgba(255,255,255,0.5); font-size: 12px; line-height: 1.7; }
        .email-footer a { color: #FFDB00; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    @php
        $statusIcons = [
            'pending'    => '🕐',
            'processing' => '⚙️',
            'completed'  => '✅',
            'cancelled'  => '❌',
        ];
        $statusMessages = [
            'pending'    => 'Your order has been received and is awaiting processing. We\'ll update you as soon as it moves forward.',
            'processing' => 'Great news — your order is now being prepared and packed. We\'ll notify you once it\'s on its way.',
            'completed'  => 'Your order has been delivered and marked as complete. We hope you love your new IKEA pieces!',
            'cancelled'  => 'Your order has been cancelled. If you paid online, a refund will be processed within 3–5 business days.',
        ];
        $icon    = $statusIcons[$order->status]    ?? '📦';
        $message = $statusMessages[$order->status] ?? 'Your order status has been updated.';
    @endphp

    {{-- Header --}}
    <div class="email-header {{ $order->status }}">
        <div class="logo-box">IKEA</div>
        <span class="status-icon">{{ $icon }}</span>
        <h1>Order {{ ucfirst($order->status) }}</h1>
        <p>Hi {{ $order->full_name }}, your order #{{ $order->id }} has been updated.</p>
    </div>

    {{-- Body --}}
    <div class="email-body">

        {{-- Order meta --}}
        <div class="order-meta">
            <div class="meta-item">
                <div class="meta-label">Order</div>
                <div class="meta-value">#{{ $order->id }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">New Status</div>
                <div class="meta-value">
                    <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Payment</div>
                <div class="meta-value">{{ ucfirst($order->payment_status) }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Total</div>
                <div class="meta-value">₱{{ number_format($order->total, 0) }}</div>
            </div>
        </div>

        {{-- Status message --}}
        <div class="message-box {{ $order->status }}">
            {{ $message }}
        </div>

        {{-- Items summary --}}
        <div class="section-title">Order Summary</div>
        @foreach($order->items as $item)
            <div class="product-row">
                <div>
                    <div class="product-name">{{ $item->product->name }}</div>
                    <div class="product-qty">Qty: {{ $item->quantity }}</div>
                </div>
                <div class="product-price">₱{{ number_format($item->price * $item->quantity, 0) }}</div>
            </div>
        @endforeach

        {{-- CTA --}}
        <div class="cta-wrap">
            <a href="{{ url('/orders/' . $order->id) }}" class="cta-btn">
                View Order Details →
            </a>
        </div>

        <p style="font-size:13px;color:#767676;text-align:center;line-height:1.6;">
            Need help? Reply to this email or contact our
            <a href="{{ url('/') }}" style="color:#0058A3;font-weight:700;">support team</a>.
        </p>

    </div>

    {{-- Footer --}}
    <div class="email-footer">
        <p>
            © {{ date('Y') }} IKEA Philippines. All rights reserved.<br>
            <a href="{{ url('/') }}">ikea.ph</a> &nbsp;·&nbsp;
            <a href="{{ url('/orders') }}">My Orders</a> &nbsp;·&nbsp;
            <a href="{{ url('/shop') }}">Shop</a>
        </p>
    </div>

</div>
</body>
</html>