<x-app-layout>
    <x-slot name="title">Order Confirmed</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">Order Confirmed!</h2>
            </div>
        </div>
    </x-slot>

    <div class="order-success-page">
        <div class="order-success-card">
            <div class="order-success-icon">✅</div>
            <h2 class="order-success-title">Thank you for your order!</h2>
            <p class="order-success-sub">
                Your order <strong style="color:var(--ikea-dark);">#{{ $order->id }}</strong>
                has been placed successfully. A confirmation email has been sent to you.
            </p>

            <div class="order-success-details">
                @foreach($order->items as $item)
                <div class="order-success-item">
                    <span style="color:var(--ikea-dark);">{{ $item->product->name }} ×{{ $item->quantity }}</span>
                    <span style="font-weight:700;">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                @endforeach
                <div class="order-success-total">
                    <span>Total</span>
                    <span>₱{{ number_format($order->total, 2) }}</span>
                </div>

                <div class="order-success-meta">
                    <div>
                        <div class="order-success-meta-label">Payment Method</div>
                        <div class="order-success-meta-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</div>
                    </div>
                    <div>
                        <div class="order-success-meta-label">Status</div>
                        <div class="order-success-meta-value">
                            <span class="order-status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <div class="order-success-meta-label">Deliver to</div>
                        <div class="order-success-meta-value">
                            {{ $order->full_name }}, {{ $order->address }},
                            {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-success-actions">
                <a href="{{ route('orders.show', $order) }}" class="customer-btn-primary">View Order Details</a>
                <a href="{{ route('orders.receipt', $order) }}" class="customer-btn-secondary">⬇ Download Receipt</a>
                <a href="{{ route('shop.index') }}" class="customer-btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </div>

</x-app-layout>