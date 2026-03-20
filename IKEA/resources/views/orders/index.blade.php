<x-app-layout>
    <x-slot name="title">My Orders</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Orders</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    You have no orders yet. <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline">Start shopping</a>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Payment</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 font-semibold">₱{{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'pending'    => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'completed'  => 'bg-green-100 text-green-700',
                                            'cancelled'  => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline text-sm font-medium">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>