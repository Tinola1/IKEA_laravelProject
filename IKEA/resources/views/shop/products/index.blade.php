@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shop</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('shop.index') }}" class="mb-6 flex gap-3 flex-wrap">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search products..."
                    class="border-gray-300 rounded-md shadow-sm w-64"
                >
                <select name="category" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
                <a href="{{ route('shop.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Reset</a>
            </form>

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                <a href="{{ route('shop.show', $product) }}" class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">No Image</div>
                    @endif
                    <div class="p-4">
                        <p class="text-xs text-gray-400 mb-1">{{ $product->category->name }}</p>
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                        <p class="text-blue-600 font-bold mt-1">₱{{ number_format($product->price, 2) }}</p>
                        <p class="text-xs mt-1 {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </p>
                    </div>
                </a>
                @empty
                <p class="text-gray-500 col-span-4">No products found.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>