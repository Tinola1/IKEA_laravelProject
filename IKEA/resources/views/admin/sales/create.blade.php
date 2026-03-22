<x-admin-layout>
    <x-slot name="title">In-Store Sale</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">In-Store Sale</h2>
                <p class="admin-page-subtitle">Process a walk-in customer purchase.</p>
            </div>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="admin-flash" style="background:#ffebee;color:#CC0008;border-left:4px solid #CC0008;margin:var(--space-md) var(--space-lg) 0;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="admin-dashboard">
        <form method="POST" action="{{ route('admin.sales.store') }}" id="saleForm">
            @csrf

            <div class="admin-form-grid">

                {{-- ── LEFT ── --}}
                <div class="admin-form-main">

                    {{-- Product selector --}}
                    <div class="admin-card" style="padding:var(--space-md);">
                        <h3 class="admin-section-title">Products</h3>

                        {{-- Search & add product --}}
                        <div class="pos-search-wrap">
                            <input type="text"
                                   id="productSearch"
                                   placeholder="🔍 Search product by name..."
                                   class="admin-input"
                                   autocomplete="off"
                                   onkeyup="searchProducts(this.value)">
                            <div id="productDropdown" class="pos-dropdown" style="display:none;"></div>
                        </div>

                        {{-- Selected items --}}
                        <div id="selectedItems" style="margin-top:var(--space-sm);">
                            <div id="emptyCart" style="text-align:center;padding:var(--space-lg);color:var(--ikea-gray);font-size:var(--text-sm);">
                                No products added yet. Search above to add items.
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="pos-total-bar">
                            <span>Total</span>
                            <span id="totalDisplay" style="font-size:22px;font-weight:900;color:var(--ikea-blue);">₱0</span>
                        </div>
                    </div>

                    {{-- Customer info --}}
                    <div class="admin-card" style="padding:var(--space-md);">
                        <h3 class="admin-card-title" style="margin-bottom:var(--space-sm);">Customer Details</h3>

                        {{-- Hidden address defaults for in-store --}}
                        <input type="hidden" name="address"  value="In-Store Purchase">
                        <input type="hidden" name="city"     value="Manila">
                        <input type="hidden" name="province" value="Metro Manila">
                        <input type="hidden" name="zip_code" value="1000">

                        {{-- Existing customer lookup --}}
                        <div class="form-group">
                            <label class="form-label">Existing Customer (optional)</label>
                            <select name="user_id" class="form-input" onchange="fillCustomer(this)">
                                <option value="">— Walk-in / New Customer —</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-phone="{{ $customer->phone }}"
                                            data-email="{{ $customer->email }}">
                                        {{ $customer->name }} — {{ $customer->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" name="full_name" id="field_name" class="form-input" value="{{ old('full_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone <span class="required">*</span></label>
                                <input type="text" name="phone" id="field_phone" class="form-input" value="{{ old('phone') }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email (for receipt)</label>
                                <input type="email" name="email" id="field_email" class="form-input" value="{{ old('email') }}" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Payment Method <span class="required">*</span></label>
                                <select name="payment_method" class="form-input" required>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cod">COD</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Notes (optional)</label>
                            <input type="text" name="notes" class="form-input" value="{{ old('notes') }}" placeholder="e.g. showroom visit, appointment #00001">
                        </div>

                    </div>

                {{-- ── RIGHT SIDEBAR ── --}}
                <div class="admin-form-sidebar">
                    <div class="admin-card" style="padding:var(--space-md);">
                        <button type="submit" class="profile-save-btn">
                            Process Sale
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="profile-cancel-link">Cancel</a>
                    </div>

                    <div class="admin-card" style="padding:var(--space-md);background:#e8f5e9;border-color:#c8e6c9;">
                        <h3 class="admin-section-title" style="border-color:#c8e6c9;">ℹ️ In-Store Sale</h3>
                        <div style="font-size:var(--text-sm);color:#2e7d32;line-height:1.7;">
                            <p>• Order status set to <strong>Processing</strong></p>
                            <p>• Payment marked as <strong>Paid</strong></p>
                            <p>• Stock reduced immediately</p>
                            <p>• Email receipt sent if email provided</p>
                        </div>
                    </div>
                </div>

            </div>

        </form>
    </div>

    {{-- Hidden product data for JS --}}
    <script>
        @php
            $productJson = $products->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'price'    => $p->price,
                'stock'    => $p->stock,
                'category' => $p->category?->name ?? '',
            ])->values();
            @endphp

            const allProducts = {!! json_encode($productJson) !!};

        let cartItems = {};

        function searchProducts(query) {
            const dropdown = document.getElementById('productDropdown');
            if (!query.trim()) { dropdown.style.display = 'none'; return; }

            const matches = allProducts.filter(p =>
                p.name.toLowerCase().includes(query.toLowerCase()) ||
                p.category.toLowerCase().includes(query.toLowerCase())
            ).slice(0, 8);

            if (!matches.length) { dropdown.style.display = 'none'; return; }

            dropdown.innerHTML = matches.map(p => `
                <div class="pos-dropdown-item" onclick="addProduct(${p.id})">
                    <div class="pos-dropdown-name">${p.name}</div>
                    <div class="pos-dropdown-meta">${p.category} · ₱${p.price.toLocaleString('en-PH')} · ${p.stock} in stock</div>
                </div>
            `).join('');
            dropdown.style.display = 'block';
        }

        function addProduct(id) {
            const product = allProducts.find(p => p.id === id);
            if (!product) return;

            if (cartItems[id]) {
                if (cartItems[id].qty >= product.stock) {
                    alert('Not enough stock for ' + product.name);
                    return;
                }
                cartItems[id].qty++;
            } else {
                cartItems[id] = { ...product, qty: 1 };
            }

            document.getElementById('productSearch').value = '';
            document.getElementById('productDropdown').style.display = 'none';
            renderCart();
        }

        function updateQty(id, delta) {
            if (!cartItems[id]) return;
            cartItems[id].qty += delta;
            if (cartItems[id].qty <= 0) {
                delete cartItems[id];
            } else if (cartItems[id].qty > cartItems[id].stock) {
                cartItems[id].qty = cartItems[id].stock;
            }
            renderCart();
        }

        function removeItem(id) {
            delete cartItems[id];
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('selectedItems');
            const emptyMsg  = document.getElementById('emptyCart');
            const items     = Object.values(cartItems);

            // Remove old hidden inputs
            document.querySelectorAll('.cart-hidden-input').forEach(el => el.remove());

            if (!items.length) {
                container.innerHTML = `<div id="emptyCart" style="text-align:center;padding:var(--space-lg);color:var(--ikea-gray);font-size:var(--text-sm);">No products added yet. Search above to add items.</div>`;
                document.getElementById('totalDisplay').textContent = '₱0';
                return;
            }

            let total = 0;
            let html  = '<div style="display:flex;flex-direction:column;gap:8px;">';

            items.forEach((item, i) => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                html += `
                    <div class="pos-cart-item">
                        <div class="pos-cart-info">
                            <div class="pos-cart-name">${item.name}</div>
                            <div class="pos-cart-price">₱${item.price.toLocaleString('en-PH')} each</div>
                        </div>
                        <div class="pos-cart-qty">
                            <button type="button" onclick="updateQty(${item.id}, -1)" class="pos-qty-btn">−</button>
                            <span class="pos-qty-val">${item.qty}</span>
                            <button type="button" onclick="updateQty(${item.id}, 1)" class="pos-qty-btn">+</button>
                        </div>
                        <div class="pos-cart-subtotal">₱${subtotal.toLocaleString('en-PH', {maximumFractionDigits:0})}</div>
                        <button type="button" onclick="removeItem(${item.id})" class="pos-cart-remove">✕</button>
                    </div>
                `;

                // Add hidden inputs for form submission
                const form = document.getElementById('saleForm');
                ['id','qty'].forEach(field => {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = `products[${i}][${field === 'id' ? 'id' : 'qty'}]`;
                    input.value = field === 'id' ? item.id : item.qty;
                    input.className = 'cart-hidden-input';
                    form.appendChild(input);
                });
            });

            html += '</div>';
            container.innerHTML = html;
            document.getElementById('totalDisplay').textContent = '₱' + total.toLocaleString('en-PH', {maximumFractionDigits:0});
        }

        function fillCustomer(select) {
            const opt = select.options[select.selectedIndex];
            document.getElementById('field_name').value  = opt.dataset.name  || '';
            document.getElementById('field_phone').value = opt.dataset.phone || '';
            document.getElementById('field_email').value = opt.dataset.email || '';
        }

        document.getElementById('saleForm').addEventListener('submit', function(e) {
            let valid = true;
            const errors = {};

            const name    = document.getElementById('field_name');
            const phone   = document.getElementById('field_phone');
            const email   = document.getElementById('field_email');
            const payment = document.querySelector('[name="payment_method"]');

            document.querySelectorAll('.js-error').forEach(el => el.remove());

            if (!Object.keys(cartItems).length) {
                const cartArea = document.getElementById('selectedItems');
                const msg = document.createElement('p');
                msg.className = 'form-error js-error';
                msg.textContent = 'Please add at least one product.';
                cartArea.parentNode.insertBefore(msg, cartArea);
                valid = false;
            }

            if (!name.value.trim()) {
                errors.full_name = 'Full name is required.';
                valid = false;
            }

            if (!phone.value.trim()) {
                errors.phone = 'Phone number is required.';
                valid = false;
            } else if (!/^[0-9+\s\-]{7,20}$/.test(phone.value.trim())) {
                errors.phone = 'Please enter a valid phone number.';
                valid = false;
            }

            if (email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                errors.email = 'Please enter a valid email address.';
                valid = false;
            }

            if (!payment.value) {
                errors.payment_method = 'Please select a payment method.';
                valid = false;
            }

            Object.keys(errors).forEach(field => {
                const input = document.querySelector('[name="' + field + '"]');
                if (!input) return;
                const msg = document.createElement('p');
                msg.className = 'form-error js-error';
                msg.textContent = errors[field];
                input.parentNode.appendChild(msg);
                input.style.borderColor = '#CC0008';
            });

            if (!valid) e.preventDefault();
        });

        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const err = this.parentNode.querySelector('.js-error');
                if (err) err.remove();
            });
        });

        // Close dropdown on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.pos-search-wrap')) {
                document.getElementById('productDropdown').style.display = 'none';
            }
        });
    </script>

</x-admin-layout>