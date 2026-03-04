<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order Confirmed!</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center">

                <div class="text-6xl mb-4">✅</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Thank you for your order!</h2>
                <p class="text-gray-500 mb-6">Your order <span class="font-bold text-gray-700">#{{ $order->id }}</span> has been placed successfully.</p>

                <div class="bg-gray-50 rounded-lg p-6 text-left mb-6">
                    <h3 class="font-bold text-gray-700 mb-4">Order Details</h3>

                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item->product->name }} x{{ $item->quantity }}</span>
                            <span class="font-medium">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-3 flex justify-between font-bold">
                        <span>Total</span>
                        <span>₱{{ number_format($order->total, 2) }}</span>
                    </div>

                    <div class="border-t mt-4 pt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Payment Method</span>
                            <p class="font-medium capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Status</span>
                            <p class="font-medium capitalize">{{ $order->status }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Deliver to</span>
                            <p class="font-medium">{{ $order->full_name }}, {{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 justify-center">
                    <a href="{{ route('orders.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">View My Orders</a>
                    <a href="{{ route('shop.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>