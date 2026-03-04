<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Management</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-black text-gray-800">{{ $products->total() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total Products</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-black text-yellow-500">{{ $lowStock }}</p>
                    <p class="text-sm text-gray-500 mt-1">Low Stock (≤5 left)</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-3xl font-black text-red-500">{{ $outOfStock }}</p>
                    <p class="text-sm text-gray-500 mt-1">Out of Stock</p>
                </div>
            </div>

            {{-- Inventory Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Adjust Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                            <td class="px-6 py-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($product->stock == 0)
                                    <span class="font-bold text-red-600">0 — Out of Stock</span>
                                @elseif($product->stock <= 5)
                                    <span class="font-bold text-yellow-600">{{ $product->stock }} — Low Stock</span>
                                @else
                                    <span class="font-bold text-green-600">{{ $product->stock }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($product->is_available)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Available</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Unavailable</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.inventory.update', $product) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="stock" value="{{ $product->stock }}" min="0" class="w-20 border-gray-300 rounded-md shadow-sm text-center text-sm">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-semibold hover:bg-blue-700">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $products->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>