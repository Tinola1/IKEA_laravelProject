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
                                <input type="text" name="full_name" id="co_full_name" class="checkout-input"
                                       value="{{ old('full_name', auth()->user()->name) }}">
                                @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="checkout-field">
                                <label class="checkout-label">Phone Number <span style="color:#CC0008;">*</span></label>
                                <input type="text" name="phone" id="co_phone" class="checkout-input"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       placeholder="+63 912 345 6789">
                                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        @php $addresses = Auth::user()->addresses()->get(); @endphp
                            @if($addresses->count())
                                <div class="form-group">
                                    <label class="form-label">Delivery Address <span class="required">*</span></label>
                                    @foreach($addresses as $addr)
                                        <label class="address-pick-option {{ $addr->is_default ? 'selected' : '' }}" id="addrOption{{ $addr->id }}">
                                            <input type="radio" name="address_id" value="{{ $addr->id }}"
                                                {{ $addr->is_default ? 'checked' : '' }}
                                                onchange="selectAddress({{ $addr->id }})">
                                            <div>
                                                <span style="font-weight:700;">{{ $addr->label }}</span>
                                                @if($addr->is_default)<span class="address-badge-default">Default</span>@endif
                                                <div style="font-size:13px;margin-top:2px;">{{ $addr->full_name }} · {{ $addr->phone }}</div>
                                                <div style="font-size:13px;color:var(--ikea-gray);">{{ $addr->oneLiner() }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                    <a href="{{ route('profile.edit') }}#address-book" class="address-btn-link" style="display:inline-block;margin-top:8px;">+ Add new address</a>
                                </div>

                                {{-- Hidden fields populated by JS from selected address --}}
                                <input type="hidden" name="address"   id="co_address"   value="{{ $addresses->firstWhere('is_default', true)?->address ?? $addresses->first()->address }}">
                                <input type="hidden" name="city"      id="co_city"      value="{{ $addresses->firstWhere('is_default', true)?->city ?? $addresses->first()->city }}">
                                <input type="hidden" name="province"  id="co_province"  value="{{ $addresses->firstWhere('is_default', true)?->province ?? $addresses->first()->province }}">
                                <input type="hidden" name="zip_code"  id="co_zip_code"  value="{{ $addresses->firstWhere('is_default', true)?->zip_code ?? $addresses->first()->zip_code }}">

                                @php $addrJson = $addresses->keyBy('id')->map(fn($a) => [
                                    'full_name' => $a->full_name, 'phone' => $a->phone,
                                    'address'   => $a->address,  'city'  => $a->city,
                                    'province'  => $a->province, 'zip_code' => $a->zip_code,
                                ])->toArray(); @endphp

                                <script>
                                const savedAddresses = @json($addrJson);
                                function selectAddress(id) {
                                    const a = savedAddresses[id];
                                    if (!a) return;
                                    document.getElementById('co_full_name').value = a.full_name;
                                    document.getElementById('co_phone').value     = a.phone;
                                    document.getElementById('co_address').value   = a.address;
                                    document.getElementById('co_city').value      = a.city;
                                    document.getElementById('co_province').value  = a.province;
                                    document.getElementById('co_zip_code').value  = a.zip_code;
                                    document.querySelectorAll('.address-pick-option').forEach(el => el.classList.remove('selected'));
                                    document.getElementById('addrOption' + id).classList.add('selected');
                                }
                                </script>

                                <style>
                                .address-pick-option { display:flex;gap:12px;align-items:flex-start;border:1.5px solid var(--ikea-border);border-radius:8px;padding:12px 14px;margin-bottom:8px;cursor:pointer;transition:border-color .15s; }
                                .address-pick-option.selected { border-color:var(--ikea-blue);background:#f0f6ff; }
                                .address-pick-option input[type=radio] { margin-top:3px;flex-shrink:0;accent-color:var(--ikea-blue); }
                                </style>
                            @else
                                {{-- No saved addresses — fall back to manual entry and nudge them to save one --}}
                                <div class="admin-flash" style="background:#fff3e0;border-left:4px solid #f57c00;margin-bottom:var(--space-sm);">
                                    You have no saved addresses. <a href="{{ route('profile.edit') }}" style="color:var(--ikea-blue);font-weight:700;">Add one in your profile</a> to speed up future checkouts.
                                </div>
                                {{-- keep existing manual address inputs here unchanged --}}
                            @endif
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