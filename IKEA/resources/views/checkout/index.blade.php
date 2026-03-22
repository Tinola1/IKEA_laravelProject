<x-app-layout>
    <x-slot name="title">Checkout</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">Checkout</h2>
                <p class="shop-page-subtitle">Complete your order details below.</p>
            </div>
        </div>
    </x-slot>

    <div class="customer-page-wide">
        <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" novalidate>
            @csrf
            <div class="checkout-layout">

                {{-- LEFT: Shipping & Payment --}}
                <div class="checkout-main">

                    <div class="customer-card">
                        <h3 class="customer-card-title">📦 Shipping Information</h3>
                        <div class="checkout-grid-2">
                            <div class="checkout-field">
                                <label class="checkout-label">Full Name <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="full_name" class="checkout-input"
                                       value="{{ old('full_name', auth()->user()->name) }}">
                                @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="checkout-field">
                                <label class="checkout-label">Phone Number <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="phone" class="checkout-input"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       placeholder="+63 912 345 6789">
                                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="checkout-field">
                            <label class="checkout-label">Street Address <span style="color:#CC0008;">*</span></label>
                            <input type="text" name="address" class="checkout-input"
                                   value="{{ old('address', auth()->user()->address) }}">
                            @error('address')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="checkout-grid-2">
                            <div class="checkout-field">
                                <label class="checkout-label">City <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="city" class="checkout-input"
                                       value="{{ old('city', auth()->user()->city) }}">
                                @error('city')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="checkout-field">
                                <label class="checkout-label">Province <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="province" class="checkout-input"
                                       value="{{ old('province', auth()->user()->province) }}">
                                @error('province')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="checkout-grid-2">
                            <div class="checkout-field">
                                <label class="checkout-label">ZIP Code <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="zip_code" class="checkout-input"
                                       value="{{ old('zip_code', auth()->user()->zip_code) }}">
                                @error('zip_code')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="checkout-field" style="margin-top:4px;">
                            <label class="checkout-label">Order Notes <span style="color:var(--ikea-gray);font-weight:400;">(optional)</span></label>
                            <textarea name="notes" rows="2" class="checkout-input"
                                      style="height:auto;padding:10px 14px;resize:vertical;"
                                      placeholder="Special delivery instructions...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="customer-card">
                        <h3 class="customer-card-title">💳 Payment Method</h3>
                        @php $selectedPayment = old('payment_method', auth()->user()->payment_method ?? 'cod'); @endphp

                        <label class="checkout-payment-option {{ $selectedPayment === 'cod' ? 'selected' : '' }}"
                               onclick="selectCheckoutPayment(this)">
                            <input type="radio" name="payment_method" value="cod"
                                   {{ $selectedPayment === 'cod' ? 'checked' : '' }}>
                            <span style="font-size:22px;">🚚</span>
                            <div>
                                <div class="checkout-payment-title">Cash on Delivery</div>
                                <div class="checkout-payment-desc">Pay in cash when your order arrives</div>
                            </div>
                        </label>
                        <label class="checkout-payment-option {{ $selectedPayment === 'gcash' ? 'selected' : '' }}"
                               onclick="selectCheckoutPayment(this)">
                            <input type="radio" name="payment_method" value="gcash"
                                   {{ $selectedPayment === 'gcash' ? 'checked' : '' }}>
                            <span style="font-size:22px;">📱</span>
                            <div>
                                <div class="checkout-payment-title">GCash</div>
                                <div class="checkout-payment-desc">Pay via GCash mobile wallet</div>
                            </div>
                        </label>
                        <label class="checkout-payment-option {{ $selectedPayment === 'bank_transfer' ? 'selected' : '' }}"
                               onclick="selectCheckoutPayment(this)">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                   {{ $selectedPayment === 'bank_transfer' ? 'checked' : '' }}>
                            <span style="font-size:22px;">🏦</span>
                            <div>
                                <div class="checkout-payment-title">Bank Transfer</div>
                                <div class="checkout-payment-desc">Transfer directly to our bank account</div>
                            </div>
                        </label>
                        @error('payment_method')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                </div>

                {{-- RIGHT: Summary --}}
                <div class="checkout-summary-sticky">
                    <div class="customer-card">
                        <h3 class="customer-card-title">🧾 Order Summary</h3>

                        @foreach($cartItems as $item)
                        <div class="checkout-summary-item">
                            <div>
                                <span class="checkout-summary-qty">{{ $item->quantity }}×</span>
                                <span class="checkout-summary-name">{{ $item->product->name }}</span>
                            </div>
                            <span class="checkout-summary-price">₱{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach

                        <div class="checkout-totals">
                            <div class="checkout-totals-row">
                                <span>Subtotal</span>
                                <span>₱{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="checkout-totals-row">
                                <span>Shipping</span>
                                <span style="color:#2e7d32;font-weight:700;">
                                    {{ $total >= 5000 ? 'Free' : '₱350.00' }}
                                </span>
                            </div>
                            <div class="checkout-totals-row grand">
                                <span>Total</span>
                                <span>₱{{ number_format($total >= 5000 ? $total : $total + 350, 2) }}</span>
                            </div>
                        </div>

                        <button type="submit" class="checkout-submit-btn">Place Order</button>
                        <a href="{{ route('cart.index') }}"
                           style="display:block;text-align:center;margin-top:10px;font-size:var(--text-sm);color:var(--ikea-gray);text-decoration:none;font-weight:600;">
                            ← Back to Cart
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
    function selectCheckoutPayment(label) {
        document.querySelectorAll('.checkout-payment-option').forEach(el => el.classList.remove('selected'));
        label.classList.add('selected');
    }

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        let valid = true;
        document.querySelectorAll('.js-error').forEach(el => el.remove());

        const rules = [
            { name: 'full_name', label: 'Full name',    required: true, minLength: 2 },
            { name: 'phone',     label: 'Phone number', required: true, minLength: 7 },
            { name: 'address',   label: 'Address',      required: true },
            { name: 'city',      label: 'City',         required: true },
            { name: 'province',  label: 'Province',     required: true },
            { name: 'zip_code',  label: 'ZIP code',     required: true },
        ];

        rules.forEach(rule => {
            const input = document.querySelector('[name="' + rule.name + '"]');
            if (!input) return;
            const val = input.value.trim();
            let error = null;
            if (rule.required && !val) error = rule.label + ' is required.';
            else if (rule.minLength && val.length < rule.minLength) error = rule.label + ' is too short.';
            if (error) {
                valid = false;
                input.style.borderColor = '#CC0008';
                const msg = document.createElement('p');
                msg.className = 'js-error';
                msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
                msg.textContent = error;
                input.parentNode.appendChild(msg);
            }
        });

        const payment = document.querySelector('[name="payment_method"]:checked');
        if (!payment) {
            valid = false;
            const msg = document.createElement('p');
            msg.className = 'js-error';
            msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
            msg.textContent = 'Please select a payment method.';
            document.querySelector('.checkout-payment-option').parentNode.appendChild(msg);
        }

        if (!valid) e.preventDefault();
    });

    document.querySelectorAll('.checkout-input').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const err = this.parentNode.querySelector('.js-error');
            if (err) err.remove();
        });
    });
    </script>

</x-app-layout>