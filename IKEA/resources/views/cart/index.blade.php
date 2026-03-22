@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">My Cart</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">My Cart</h2>
                @if(!$cartItems->isEmpty())
                    <p class="shop-page-subtitle">{{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}</p>
                @endif
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

        @if($cartItems->isEmpty())
            <div class="customer-card customer-empty">
                <div style="font-size:48px;margin-bottom:var(--space-sm);">🛒</div>
                <h3 style="font-weight:700;color:var(--ikea-dark);margin-bottom:8px;">Your cart is empty</h3>
                <p style="margin-bottom:var(--space-md);">Looks like you haven't added anything yet.</p>
                <a href="{{ route('shop.index') }}" class="customer-btn-primary">Start Shopping</a>
            </div>
        @else
            <div class="customer-card" style="padding:0;overflow:hidden;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" class="cart-product-img">
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $item->product->name }}</div>
                                        <div style="font-size:11px;color:var(--ikea-gray);">{{ $item->product->category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>₱{{ number_format($item->product->price, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item) }}" method="POST"
                                      style="display:flex;align-items:center;gap:8px;">
                                    @csrf @method('PATCH')
                                    <input type="number" name="quantity"
                                           value="{{ $item->quantity }}"
                                           min="1" max="{{ $item->product->stock }}"
                                           class="cart-qty-input">
                                    <button type="submit" class="cart-update-btn">Update</button>
                                </form>
                            </td>
                            <td style="font-weight:700;">₱{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item) }}" method="POST"
                                      onsubmit="return confirm('Remove this item?')">
                                    @csrf @method('DELETE')
                                    <button class="cart-remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="cart-total-bar">
                    <div class="cart-total-label">Total: ₱{{ number_format($total, 2) }}</div>
                    <a href="{{ route('checkout.index') }}" class="customer-btn-primary">
                        Proceed to Checkout →
                    </a>
                </div>
            </div>

            <a href="{{ route('shop.index') }}"
               style="font-size:var(--text-sm);color:var(--ikea-gray);text-decoration:none;font-weight:600;">
                ← Continue Shopping
            </a>
        @endif

    </div>

    <script>
    document.querySelectorAll('form[action*="cart.update"], form[action*="cart/update"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const input = this.querySelector('[name="quantity"]');
            const existing = this.querySelector('.js-error');
            if (existing) existing.remove();
            const val = parseInt(input.value);
            const max = parseInt(input.getAttribute('max')) || 999;
            let error = null;
            if (!input.value || isNaN(val)) error = 'Please enter a quantity.';
            else if (val < 1) error = 'Minimum quantity is 1.';
            else if (val > max) error = 'Only ' + max + ' in stock.';
            if (error) {
                e.preventDefault();
                input.style.borderColor = '#CC0008';
                const msg = document.createElement('p');
                msg.className = 'js-error';
                msg.style.cssText = 'color:#CC0008;font-size:11px;margin-top:3px;font-weight:600;';
                msg.textContent = error;
                input.parentNode.appendChild(msg);
            }
        });
        const input = form.querySelector('[name="quantity"]');
        if (input) {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const err = this.parentNode.querySelector('.js-error');
                if (err) err.remove();
            });
        }
    });
    </script>

</x-app-layout>