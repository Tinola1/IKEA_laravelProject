@php use Illuminate\Support\Facades\Storage; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Cart</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            @if($cartItems->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Your cart is empty. <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline">Continue Shopping</a>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Product</th>
                                <th class="py-2">Price</th>
                                <th class="py-2">Quantity</th>
                                <th class="py-2">Subtotal</th>
                                <th class="py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr class="border-b">
                                <td class="py-3 flex items-center gap-3">
                                    @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" class="w-14 h-14 object-cover rounded">
                                    @endif
                                    <span>{{ $item->product->name }}</span>
                                </td>
                                <td class="py-3">₱{{ number_format($item->product->price, 2) }}</td>
                                <td class="py-3">
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-16 border-gray-300 rounded-md shadow-sm text-center">
                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">Update</button>
                                    </form>
                                </td>
                                <td class="py-3">₱{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                <td class="py-3">
                                    <form action="{{ route('cart.remove', $item) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Total --}}
                    <div class="mt-6 flex justify-end items-center gap-6">
                        <p class="text-xl font-bold text-gray-800">Total: ₱{{ number_format($total, 2) }}</p>
                        <a href="{{ route('checkout.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('shop.index') }}" class="text-gray-500 hover:underline">← Continue Shopping</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>