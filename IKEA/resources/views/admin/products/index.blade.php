@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">All Products</h3>
                    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Product</a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">#</th>
                            <th class="py-2">Image</th>
                            <th class="py-2">Name</th>
                            <th class="py-2">Category</th>
                            <th class="py-2">Price</th>
                            <th class="py-2">Stock</th>
                            <th class="py-2">Available</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b">
                            <td class="py-2">{{ $loop->iteration }}</td>
                            <td class="py-2">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    <span class="text-gray-400">No image</span>
                                @endif
                            </td>
                            <td class="py-2">{{ $product->name }}</td>
                            <td class="py-2">{{ $product->category->name }}</td>
                            <td class="py-2">₱{{ number_format($product->price, 2) }}</td>
                            <td class="py-2">{{ $product->stock }}</td>
                            <td class="py-2">
                                @if($product->is_available)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Yes</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">No</span>
                                @endif
                            </td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="py-4 text-center text-gray-500">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>