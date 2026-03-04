<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            {{-- Update Status Form --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-700 mb-4">Update Order Status</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex flex-wrap gap-4 items-end">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                        <select name="status" class="border-gray-300 rounded-md shadow-sm">
                            @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" class="border-gray-300 rounded-md shadow-sm">
                            @foreach(['unpaid', 'paid'] as $status)
                                <option value="{{ $status }}" {{ $order->payment_status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700">
                        Update
                    </button>
                </form>
            </div>

            {{-- Customer Info --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-700 mb-4">Customer Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Name</p><p class="font-medium">{{ $order->user->name }}</p></div>
                    <div><p class="text-gray-500">Email</p><p class="font-medium">{{ $order->user->email }}</p></div>
                    <div><p class="text-gray-500">Full Name</p><p class="font-medium">{{ $order->full_name }}</p></div>
                    <div><p class="text-gray-500">Phone</p><p class="font-medium">{{ $order->phone }}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Address</p><p class="font-medium">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->zip_code }}</p></div>
                    @if($order->notes)
                    <div class="col-span-2"><p class="text-gray-500">Notes</p><p class="font-medium">{{ $order->notes }}</p></div>
                    @endif
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

            <a href="{{ route('admin.orders.index') }}" class="inline-block bg-gray-100 text-gray-700 px-5 py-2 rounded-lg font-semibold hover:bg-gray-200">← Back to Orders</a>
        </div>
    </div>
</x-app-layout>