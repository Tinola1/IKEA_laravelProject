<x-app-layout>
    <x-slot name="title">Checkout</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" novalidate>
                @csrf
                <div class="flex flex-col lg:flex-row gap-6">

                    {{-- Left: Shipping & Payment --}}
                    <div class="flex-1 space-y-6">

                        {{-- Shipping Info --}}
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-4">Shipping Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('full_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Street Address</label>
                                    <input type="text" name="address" value="{{ old('address') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="city" value="{{ old('city') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Province</label>
                                    <input type="text" name="province" value="{{ old('province') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('province') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ZIP Code</label>
                                    <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('zip_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Order Notes (optional)</label>
                                    <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-4">Payment Method</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-400 transition {{ old('payment_method', 'cod') == 'cod' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }} class="text-blue-600">
                                    <div>
                                        <div class="font-semibold">💵 Cash on Delivery</div>
                                        <div class="text-sm text-gray-500">Pay when your order arrives</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-400 transition {{ old('payment_method') == 'gcash' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" name="payment_method" value="gcash" {{ old('payment_method') == 'gcash' ? 'checked' : '' }} class="text-blue-600">
                                    <div>
                                        <div class="font-semibold">📱 GCash</div>
                                        <div class="text-sm text-gray-500">Pay via GCash mobile wallet</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:border-blue-400 transition {{ old('payment_method') == 'bank_transfer' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} class="text-blue-600">
                                    <div>
                                        <div class="font-semibold">🏦 Bank Transfer</div>
                                        <div class="text-sm text-gray-500">Transfer to our bank account</div>
                                    </div>
                                </label>
                            </div>
                            @error('payment_method') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Right: Order Summary --}}
                    <div class="lg:w-96">
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 sticky top-4">
                            <h3 class="text-lg font-bold mb-4">Order Summary</h3>

                            <div class="space-y-3 mb-4">
                                @foreach($cartItems as $item)
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 text-xs rounded px-1.5 py-0.5">{{ $item->quantity }}x</span>
                                        <span class="text-gray-700">{{ $item->product->name }}</span>
                                    </div>
                                    <span class="font-medium">₱{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Subtotal</span>
                                    <span>₱{{ number_format($total, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Shipping</span>
                                    <span class="text-green-600">Free</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t pt-2">
                                    <span>Total</span>
                                    <span>₱{{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                Place Order
                            </button>
                            <a href="{{ route('cart.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:underline">← Back to Cart</a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        let valid = true;
        document.querySelectorAll('.js-error').forEach(el => el.remove());
        document.querySelectorAll('.js-invalid').forEach(el => el.style.borderColor = '');

        const rules = [
            { name: 'full_name',       label: 'Full name',       required: true, minLength: 2 },
            { name: 'phone',           label: 'Phone number',    required: true, minLength: 7 },
            { name: 'address',         label: 'Address',         required: true },
            { name: 'city',            label: 'City',            required: true },
            { name: 'province',        label: 'Province',        required: true },
            { name: 'zip_code',        label: 'ZIP code',        required: true },
        ];

        rules.forEach(rule => {
            const input = document.querySelector('[name="' + rule.name + '"]');
            if (!input) return;
            const val = input.value.trim();
            let error = null;

            if (rule.required && !val) {
                error = rule.label + ' is required.';
            } else if (rule.minLength && val.length < rule.minLength) {
                error = rule.label + ' is too short.';
            }

            if (error) {
                valid = false;
                input.style.borderColor = '#CC0008';
                input.classList.add('js-invalid');
                const msg = document.createElement('p');
                msg.className = 'js-error';
                msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
                msg.textContent = error;
                input.parentNode.appendChild(msg);
            }
        });

        // Payment method
        const payment = document.querySelector('[name="payment_method"]:checked');
        if (!payment) {
            valid = false;
            const msg = document.createElement('p');
            msg.className = 'js-error';
            msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
            msg.textContent = 'Please select a payment method.';
            const paymentSection = document.querySelector('[name="payment_method"]').closest('div');
            paymentSection.appendChild(msg);
        }

        if (!valid) e.preventDefault();
    });

    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            this.classList.remove('js-invalid');
            const err = this.parentNode.querySelector('.js-error');
            if (err) err.remove();
        });
    });
    </script>
</x-app-layout>