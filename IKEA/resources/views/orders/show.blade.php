<x-app-layout>
    <x-slot name="title">Order #{{ $order->id }}</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">Order #{{ $order->id }}</h2>
                <p class="shop-page-subtitle">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="order-detail-page">

        @if(session('success'))
            <div class="customer-flash success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="customer-flash error">{{ session('error') }}</div>
        @endif

        {{-- Status Card --}}
        <div class="customer-card">
            <div class="order-meta-bar">
                <div>
                    <div class="order-meta-text">
                        Payment: <strong>{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</strong>
                        —
                        <span style="color:{{ $order->payment_status === 'paid' ? '#2e7d32' : '#CC0008' }};font-weight:700;">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
                <span class="order-status-badge status-{{ $order->status }}" style="font-size:13px;padding:5px 14px;">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        {{-- Items --}}
        <div class="customer-card">
            <h3 class="customer-card-title">Items Ordered</h3>
            @foreach($order->items as $item)
            <div class="order-items-row">
                <div>
                    <div class="order-items-name">{{ $item->product->name }}</div>
                    <div class="order-items-meta">₱{{ number_format($item->price, 2) }} × {{ $item->quantity }}</div>
                </div>
                <div class="order-items-price">₱{{ number_format($item->price * $item->quantity, 2) }}</div>
            </div>
            @endforeach
            <div class="order-total-row">
                <span>Total</span>
                <span>₱{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        {{-- Shipping --}}
        <div class="customer-card">
            <h3 class="customer-card-title">Shipping Information</h3>
            <div class="order-info-grid">
                <div>
                    <div class="order-info-label">Full Name</div>
                    <div class="order-info-value">{{ $order->full_name }}</div>
                </div>
                <div>
                    <div class="order-info-label">Phone</div>
                    <div class="order-info-value">{{ $order->phone }}</div>
                </div>
                <div style="grid-column:1/-1;">
                    <div class="order-info-label">Address</div>
                    <div class="order-info-value">
                        {{ $order->address }}, {{ $order->city }},
                        {{ $order->province }} {{ $order->zip_code }}
                    </div>
                </div>
                @if($order->notes)
                <div style="grid-column:1/-1;">
                    <div class="order-info-label">Notes</div>
                    <div class="order-info-value">{{ $order->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="order-actions">
            <a href="{{ route('orders.index') }}" class="customer-btn-secondary">← Back to Orders</a>
            <a href="{{ route('orders.receipt', $order) }}" class="customer-btn-secondary">⬇ Download Receipt</a>
            @if(in_array($order->status, ['pending', 'processing']))
                <form action="{{ route('orders.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf @method('PATCH')
                    <button class="customer-btn-danger">Cancel Order</button>
                </form>
            @endif
        </div>

    </div>

</x-app-layout>