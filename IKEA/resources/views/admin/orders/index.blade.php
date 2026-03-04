<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Orders</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                @if(session('success'))
                    <div class="p-4 bg-green-100 text-green-800">{{ session('success') }}</div>
                @endif

                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">#{{ $order->id }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-semibold">₱{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $payColors = ['unpaid' => 'bg-red-100 text-red-700', 'paid' => 'bg-green-100 text-green-700'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
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
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline text-sm font-medium">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No orders yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>