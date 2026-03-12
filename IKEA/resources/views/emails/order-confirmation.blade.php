<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed — IKEA Philippines</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f0;
            color: #111;
            padding: 32px 16px;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Header */
        .email-header {
            background: #0058A3;
            border-radius: 10px 10px 0 0;
            padding: 32px;
            text-align: center;
        }
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
        .email-header h1 {
            color: white;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .email-header p {
            color: rgba(255,255,255,0.8);
            font-size: 15px;
            margin-top: 8px;
        }

        /* Body */
        .email-body {
            background: white;
            padding: 32px;
        }

        /* Order meta strip */
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
        .meta-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #767676;
        }
        .meta-value {
            font-size: 15px;
            font-weight: 800;
            color: #111;
            margin-top: 3px;
        }

        /* Section title */
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

        /* Product rows */
        .product-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }
        .product-row:last-child { border-bottom: none; }
        .product-name { font-size: 14px; font-weight: 700; }
        .product-qty  { font-size: 13px; color: #767676; margin-top: 2px; }
        .product-price { font-size: 14px; font-weight: 800; white-space: nowrap; }

        /* Totals */
        .totals {
            background: #f5f5f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0 28px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 4px 0;
            color: #767676;
        }
        .totals-row.grand {
            font-size: 18px;
            font-weight: 900;
            color: #111;
            border-top: 2px solid #e5e5e5;
            margin-top: 8px;
            padding-top: 12px;
        }

        /* Delivery details */
        .delivery-box {
            border: 1.5px solid #e5e5e5;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .delivery-box p {
            font-size: 14px;
            line-height: 1.6;
            color: #444;
        }
        .delivery-box strong { color: #111; }

        /* Payment method badge */
        .payment-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1565c0;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 40px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* CTA button */
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
            letter-spacing: 0.3px;
        }

        /* Footer */
        .email-footer {
            background: #111;
            border-radius: 0 0 10px 10px;
            padding: 24px 32px;
            text-align: center;
        }
        .email-footer p {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            line-height: 1.7;
        }
        .email-footer a { color: #FFDB00; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="email-header">
        <div class="logo-box">IKEA</div>
        <h1>Your order is confirmed! 🎉</h1>
        <p>Thank you, {{ $order->full_name }}. We've received your order and it's being processed.</p>
    </div>

    {{-- Body --}}
    <div class="email-body">

        {{-- Order meta --}}
        <div class="order-meta">
            <div class="meta-item">
                <div class="meta-label">Order Number</div>
                <div class="meta-value">#{{ $order->id }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Date</div>
                <div class="meta-value">{{ $order->created_at->format('M d, Y') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Status</div>
                <div class="meta-value">{{ ucfirst($order->status) }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Total</div>
                <div class="meta-value">₱{{ number_format($order->total, 0) }}</div>
            </div>
        </div>

        {{-- Items --}}
        <div class="section-title">Items Ordered</div>
        @foreach($order->items as $item)
            <div class="product-row">
                <div>
                    <div class="product-name">{{ $item->product->name }}</div>
                    <div class="product-qty">Qty: {{ $item->quantity }}</div>
                </div>
                <div class="product-price">₱{{ number_format($item->price * $item->quantity, 0) }}</div>
            </div>
        @endforeach

        {{-- Totals --}}
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>₱{{ number_format($order->total, 0) }}</span>
            </div>
            <div class="totals-row">
                <span>Delivery</span>
                <span>{{ $order->total >= 5000 ? 'Free' : '₱350' }}</span>
            </div>
            <div class="totals-row grand">
                <span>Total</span>
                <span>₱{{ number_format($order->total >= 5000 ? $order->total : $order->total + 350, 0) }}</span>
            </div>
        </div>

        {{-- Delivery details --}}
        <div class="section-title">Delivery Details</div>
        <div class="delivery-box">
            <p>
                <strong>{{ $order->full_name }}</strong><br>
                {{ $order->address }}<br>
                {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}<br>
                📞 {{ $order->phone }}
            </p>
            <div class="payment-badge">
                Payment: {{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}
            </div>
            @if($order->notes)
                <p style="margin-top:12px;font-size:13px;color:#767676;">
                    <strong>Notes:</strong> {{ $order->notes }}
                </p>
            @endif
        </div>

        {{-- CTA --}}
        <div class="cta-wrap">
            <a href="{{ url('/orders/' . $order->id) }}" class="cta-btn">
                Track My Order →
            </a>
        </div>

        <p style="font-size:13px;color:#767676;text-align:center;line-height:1.6;">
            Questions about your order? Reply to this email or visit our
            <a href="{{ url('/') }}" style="color:#0058A3;font-weight:700;">Help Centre</a>.
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