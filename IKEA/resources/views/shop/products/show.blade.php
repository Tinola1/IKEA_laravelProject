@php use Illuminate\Support\Facades\Storage; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row gap-8">

                {{-- Product Image --}}
                <div class="md:w-1/2">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" class="w-full rounded-lg object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400 rounded-lg">No Image</div>
                    @endif
                </div>

                {{-- Product Details --}}
                <div class="md:w-1/2">
                    <p class="text-sm text-gray-400 mb-1">{{ $product->category->name }}</p>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>
                    <p class="text-3xl font-bold text-blue-600 mb-4">₱{{ number_format($product->price, 2) }}</p>

                    <p class="text-gray-600 mb-4">{{ $product->description ?? 'No description available.' }}</p>

                    <p class="mb-4 {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                        {{ $product->stock > 0 ? '✓ In Stock (' . $product->stock . ' available)' : '✗ Out of Stock' }}
                    </p>

                    @auth
    @if($product->stock > 0)
        <form action="{{ route('cart.add', $product) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                Add to Cart
            </button>
        </form>
    @else
        <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed">
            Unavailable
        </button>
    @endif
@else
    <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
        Login to Add to Cart
    </a>
@endauth

                    <a href="{{ route('shop.index') }}" class="block text-center mt-3 text-gray-500 hover:underline">← Back to Shop</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>