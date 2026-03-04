<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            {{-- Order Status --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y h:i A') }}</p>
                        <p class="text-sm text-gray-500 mt-1">Payment: <span class="font-medium capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span> —
                            <span class="{{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-red-500' }} font-medium capitalize">{{ $order->payment_status }}</span>
                        </p>
                    </div>
                    @php
                        $colors = [
                            'pending'    => 'bg-yellow-100 text-yellow-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'completed'  => 'bg-green-100 text-green-700',
                            'cancelled'  => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-700 mb-4">Items Ordered</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b last:border-0">
                        <div>
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">₱{{ number_format($item->price, 2) }} x {{ $item->quantity }}</p>
                        </div>
                        <p class="font-bold">₱{{ number_format($item->price * $item->quantity, 2) }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between font-bold text-lg mt-4 pt-4 border-t">
                    <span>Total</span>
                    <span>₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-700 mb-4">Shipping Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Full Name</p><p class="font-medium">{{ $order->full_name }}</p></div>
                    <div><p class="text-gray-500">Phone</p><p class="font-medium">{{ $order->phone }}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Address</p><p class="font-medium">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}</p></div>
                    @if($order->notes)
                    <div class="col-span-2"><p class="text-gray-500">Notes</p><p class="font-medium">{{ $order->notes }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('orders.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg font-semibold hover:bg-gray-200">← Back to Orders</a>
                @if(in_array($order->status, ['pending', 'processing']))
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        @method('PATCH')
                        <button class="bg-red-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-red-600">Cancel Order</button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>