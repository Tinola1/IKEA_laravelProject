<x-app-layout>
    <x-slot name="title">My Orders</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">My Orders</h2>
                <p class="shop-page-subtitle">Track and manage your purchases.</p>
            </div>
        </div>
    </x-slot>

    <div class="customer-page">

        @if(session('success'))
            <div class="customer-flash success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="customer-flash error">{{ session('error') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="customer-card customer-empty">
                <div style="font-size:48px;margin-bottom:var(--space-sm);">📦</div>
                <h3 style="font-weight:700;color:var(--ikea-dark);margin-bottom:8px;">No orders yet</h3>
                <p style="margin-bottom:var(--space-md);">Start shopping to place your first order.</p>
                <a href="{{ route('shop.index') }}" class="customer-btn-primary">Start Shopping</a>
            </div>
        @else
            <div class="customer-card" style="padding:0;overflow:hidden;">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td style="font-weight:700;color:var(--ikea-blue);">#{{ $order->id }}</td>
                            <td style="color:var(--ikea-gray);font-size:12px;">{{ $order->created_at->format('M d, Y') }}</td>
                            <td style="font-weight:700;">₱{{ number_format($order->total, 2) }}</td>
                            <td style="font-size:13px;text-transform:capitalize;">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                            <td>
                                <span class="order-status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="table-action-link">View →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($orders->hasPages())
                    <div style="padding:12px 16px;border-top:1px solid var(--ikea-border);">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>

</x-app-layout>