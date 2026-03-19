<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt #{{ $order->id }} — IKEA Philippines</title>
    <style>
        /* DomPDF works best with inline styles and simple CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #111111;
            background: #ffffff;
        }

        /* ── HEADER ── */
        .header {
            background: #0058A3;
            color: white;
            padding: 28px 36px;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }

        .logo-box {
            background: #FFDB00;
            color: #0058A3;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 3px;
            padding: 5px 14px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .header-company {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.5px;
        }
        .header-receipt-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header-receipt-num {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            margin-top: 4px;
        }

        /* ── BODY ── */
        .body { padding: 28px 36px; }

        /* ── INFO ROW ── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 16px;
        }
        .info-col:last-child { padding-right: 0; padding-left: 16px; text-align: right; }

        .info-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #767676;
            margin-bottom: 6px;
            border-bottom: 1.5px solid #e5e5e5;
            padding-bottom: 4px;
        }
        .info-line {
            font-size: 13px;
            line-height: 1.7;
            color: #333;
        }
        .info-line strong { color: #111; }

        /* ── STATUS BADGES ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-pending    { background: #fff3e0; color: #e65100; }
        .badge-processing { background: #e3f2fd; color: #1565c0; }
        .badge-completed  { background: #e8f5e9; color: #2e7d32; }
        .badge-cancelled  { background: #ffebee; color: #b71c1c; }
        .badge-paid       { background: #e8f5e9; color: #2e7d32; }
        .badge-unpaid     { background: #ffebee; color: #b71c1c; }

        /* ── ITEMS TABLE ── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #767676;
            border-bottom: 2px solid #0058A3;
            padding-bottom: 6px;
            margin-bottom: 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #f5f5f0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #767676;
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }
        .items-table th.right { text-align: right; }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }
        .items-table td.right { text-align: right; }
        .item-name { font-weight: 700; font-size: 13px; color: #111; }
        .item-desc { font-size: 11px; color: #767676; margin-top: 2px; }

        /* ── TOTALS ── */
        .totals-wrap {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .totals-spacer { display: table-cell; width: 55%; }
        .totals-box    { display: table-cell; width: 45%; }

        .totals-row {
            display: table;
            width: 100%;
            padding: 5px 0;
        }
        .totals-label {
            display: table-cell;
            font-size: 12px;
            color: #767676;
        }
        .totals-value {
            display: table-cell;
            font-size: 12px;
            font-weight: 700;
            text-align: right;
            color: #111;
        }
        .totals-divider {
            border: none;
            border-top: 1.5px solid #e5e5e5;
            margin: 6px 0;
        }
        .totals-grand .totals-label {
            font-size: 14px;
            font-weight: 900;
            color: #111;
        }
        .totals-grand .totals-value {
            font-size: 16px;
            font-weight: 900;
            color: #0058A3;
        }

        /* ── PAYMENT METHOD ── */
        .payment-row {
            background: #f5f5f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: table;
            width: 100%;
        }
        .payment-left  { display: table-cell; vertical-align: middle; }
        .payment-right { display: table-cell; vertical-align: middle; text-align: right; }
        .payment-label { font-size: 11px; color: #767676; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .payment-value { font-size: 13px; font-weight: 700; color: #111; margin-top: 2px; }

        /* ── NOTES ── */
        .notes-box {
            background: #fffde7;
            border-left: 3px solid #FFDB00;
            padding: 10px 14px;
            margin-bottom: 24px;
            font-size: 12px;
            color: #555;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 2px solid #e5e5e5;
            padding-top: 16px;
            text-align: center;
            color: #767676;
            font-size: 11px;
            line-height: 1.8;
        }
        .footer strong { color: #111; }

        /* ── WATERMARK for cancelled ── */
        .cancelled-watermark {
            position: fixed;
            top: 40%;
            left: 10%;
            font-size: 80px;
            font-weight: 900;
            color: rgba(204,0,8,0.08);
            transform: rotate(-30deg);
            letter-spacing: 8px;
            z-index: -1;
        }
    </style>
</head>
<body>

    @if($order->status === 'cancelled')
        <div class="cancelled-watermark">CANCELLED</div>
    @endif

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="logo-box">IKEA</div>
                <div class="header-company">IKEA Philippines · ikea.ph</div>
            </div>
            <div class="header-right">
                <div class="header-receipt-title">RECEIPT</div>
                <div class="header-receipt-num">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    <div class="body">

        {{-- ── ORDER + CUSTOMER INFO ── --}}
        <div class="info-row">
            <div class="info-col">
                <div class="info-title">Bill To</div>
                <div class="info-line">
                    <strong>{{ $order->full_name }}</strong><br>
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}<br>
                    {{ $order->phone }}<br>
                    {{ $order->user?->email }}
                </div>
            </div>
            <div class="info-col">
                <div class="info-title">Order Details</div>
                <div class="info-line">
                    <strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}<br>
                    <strong>Order #:</strong> {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Status:</strong>
                    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span><br>
                    <strong>Payment:</strong>
                    <span class="badge badge-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
            </div>
        </div>

        {{-- ── ITEMS ── --}}
        <div class="section-title">Items Ordered</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:45%;">Product</th>
                    <th class="right" style="width:15%;">Unit Price</th>
                    <th class="right" style="width:15%;">Qty</th>
                    <th class="right" style="width:25%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-desc">{{ $item->product->category?->name }}</div>
                        </td>
                        <td class="right">PHP {{ number_format($item->price, 2) }}</td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">PHP {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── TOTALS ── --}}
        <div class="totals-wrap">
            <div class="totals-spacer"></div>
            <div class="totals-box">
                <div class="totals-row">
                    <div class="totals-label">Subtotal</div>
                    <div class="totals-value">PHP {{ number_format($order->total, 2) }}</div>
                </div>
                <div class="totals-row">
                    <div class="totals-label">Delivery Fee</div>
                    <div class="totals-value">
                        @if($order->total >= 5000)
                            Free
                        @else
                            PHP 350.00
                        @endif
                    </div>
                </div>
                <hr class="totals-divider">
                <div class="totals-row totals-grand">
                    <div class="totals-label">Total</div>
                    <div class="totals-value">
                        PHP {{ number_format($order->total >= 5000 ? $order->total : $order->total + 350, 2) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PAYMENT METHOD ── --}}
        <div class="payment-row">
            <div class="payment-left">
                <div class="payment-label">Payment Method</div>
                <div class="payment-value">
                    {{ match($order->payment_method) {
                        'cod'           => 'Cash on Delivery',
                        'gcash'         => 'GCash',
                        'bank_transfer' => 'Bank Transfer',
                        default         => ucfirst($order->payment_method),
                    } }}
                </div>
            </div>
            <div class="payment-right">
                <div class="payment-label">Payment Status</div>
                <div class="payment-value">
                    <span class="badge badge-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── NOTES ── --}}
        @if($order->notes)
            <div class="notes-box">
                <strong>Order Notes:</strong> {{ $order->notes }}
            </div>
        @endif

        {{-- ── FOOTER ── --}}
        <div class="footer">
            <strong>Thank you for shopping with IKEA Philippines!</strong><br>
            For questions about your order, email us at support@ikea.ph or visit ikea.ph<br>
            This is an official receipt generated on {{ now()->format('F d, Y \a\t h:i A') }}.<br>
            <br>
            © {{ date('Y') }} IKEA Philippines · All rights reserved
        </div>

    </div>

</body>
</html>