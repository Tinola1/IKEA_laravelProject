<x-admin-layout>
    <x-slot name="title">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
                <p class="admin-page-subtitle">{{ $order->created_at->format('F d, Y · h:i A') }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="admin-btn-primary" style="background:#fff;color:var(--ikea-blue);border:1.5px solid var(--ikea-blue);">← Back to Orders</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">
        <div style="display:grid;grid-template-columns:1fr 340px;gap:var(--space-md);align-items:start;">

            {{-- LEFT COLUMN --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);">

                {{-- ORDER ITEMS --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Items Ordered</h3>
                    <table class="admin-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align:right;">Unit Price</th>
                                <th style="text-align:right;">Qty</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="table-product-name">{{ $item->product->name }}</td>
                                <td style="text-align:right;">₱{{ number_format($item->price, 2) }}</td>
                                <td style="text-align:right;">{{ $item->quantity }}</td>
                                <td style="text-align:right;font-weight:700;">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="display:flex;justify-content:flex-end;margin-top:14px;padding-top:14px;border-top:1px solid var(--ikea-border);">
                        <div style="text-align:right;">
                            <div style="font-size:13px;color:var(--ikea-gray);margin-bottom:4px;">Order Total</div>
                            <div style="font-size:22px;font-weight:900;color:var(--ikea-blue);">₱{{ number_format($order->total, 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- CUSTOMER INFO --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Customer Information</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <div class="filter-label" style="margin-bottom:3px;">Account Name</div>
                            <div style="font-size:13px;font-weight:700;">{{ $order->user->name }}</div>
                        </div>
                        <div>
                            <div class="filter-label" style="margin-bottom:3px;">Email</div>
                            <div style="font-size:13px;">{{ $order->user->email }}</div>
                        </div>
                        <div>
                            <div class="filter-label" style="margin-bottom:3px;">Full Name (Recipient)</div>
                            <div style="font-size:13px;font-weight:700;">{{ $order->full_name }}</div>
                        </div>
                        <div>
                            <div class="filter-label" style="margin-bottom:3px;">Phone</div>
                            <div style="font-size:13px;">{{ $order->phone }}</div>
                        </div>
                        <div style="grid-column:span 2;">
                            <div class="filter-label" style="margin-bottom:3px;">Delivery Address</div>
                            <div style="font-size:13px;">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}</div>
                        </div>
                        @if($order->notes)
                        <div style="grid-column:span 2;">
                            <div class="filter-label" style="margin-bottom:3px;">Order Notes</div>
                            <div style="font-size:13px;background:#fffde7;border-left:3px solid #FFDB00;padding:8px 12px;border-radius:0 6px 6px 0;">
                                {{ $order->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);">

                {{-- STATUS PANEL --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Order Status</h3>

                    <div style="margin-bottom:12px;">
                        <div class="filter-label" style="margin-bottom:4px;">Current Status</div>
                        <span class="order-status-badge status-{{ $order->status }}" style="font-size:13px;padding:5px 12px;">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div style="margin-bottom:16px;">
                        <div class="filter-label" style="margin-bottom:4px;">Payment Status</div>
                        <span class="order-status-badge {{ $order->payment_status === 'paid' ? 'status-completed' : 'status-cancelled' }}" style="font-size:13px;padding:5px 12px;">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf @method('PATCH')
                        <div style="margin-bottom:12px;">
                            <label class="filter-label" style="display:block;margin-bottom:5px;">Update Order Status</label>
                            <select name="status" class="admin-select" style="width:100%;">
                                @foreach(['pending','processing','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:16px;">
                            <label class="filter-label" style="display:block;margin-bottom:5px;">Update Payment Status</label>
                            <select name="payment_status" class="admin-select" style="width:100%;">
                                @foreach(['unpaid','paid'] as $s)
                                    <option value="{{ $s }}" {{ $order->payment_status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="admin-btn-primary" style="width:100%;">Save Changes</button>
                    </form>
                </div>

                {{-- ORDER SUMMARY --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Order Summary</h3>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--ikea-gray);">Order ID</span>
                            <span class="order-id">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--ikea-gray);">Date Placed</span>
                            <span>{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--ikea-gray);">Items</span>
                            <span>{{ $order->items->sum('quantity') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:var(--ikea-gray);">Payment Method</span>
                            <span>{{ match($order->payment_method) {
                                'cod' => 'Cash on Delivery',
                                'gcash' => 'GCash',
                                'bank_transfer' => 'Bank Transfer',
                                default => ucfirst($order->payment_method)
                            } }}</span>
                        </div>
                        <div style="border-top:1px solid var(--ikea-border);padding-top:8px;display:flex;justify-content:space-between;font-weight:700;">
                            <span>Total</span>
                            <span style="color:var(--ikea-blue);">₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- RECEIPT --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:10px;">Receipt</h3>
                    <a href="{{ route('orders.receipt', $order) }}" target="_blank"
                       class="admin-btn-primary" style="display:block;text-align:center;background:#fff;color:var(--ikea-blue);border:1.5px solid var(--ikea-blue);text-decoration:none;">
                        ⬇ Download PDF Receipt
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>