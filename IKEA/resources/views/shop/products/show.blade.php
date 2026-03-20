@php use Illuminate\Support\Facades\Storage; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <p class="shop-breadcrumb">
                    <a href="{{ route('shop.index') }}">Shop</a>
                    <span aria-hidden="true"> / </span>
                    <a href="{{ route('shop.index') }}?category={{ $product->category->id }}">
                        {{ $product->category->name }}
                    </a>
                    <span aria-hidden="true"> / </span>
                    <span>{{ $product->name }}</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="shop-page">
        <div class="product-detail-wrapper">

            {{-- ── IMAGE PANEL ── --}}
            <div class="product-detail-image">
                {{-- Main image --}}
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="product-detail-img"
                        id="mainImage">
                @else
                    <div class="product-detail-img-placeholder" aria-hidden="true">
                        {{ ['🛋️','🛏️','🪑','🍳','🪞','💡','🪴','🛁'][($product->id - 1) % 8] }}
                    </div>
                @endif

                {{-- Thumbnail strip --}}
                @if($product->image && $product->productImages->isNotEmpty())
                <div class="product-thumbnails">
                    {{-- Primary image thumb --}}
                    <img src="{{ Storage::url($product->image) }}"
                        class="product-thumb active"
                        onclick="switchImage(this, '{{ Storage::url($product->image) }}')">
                    {{-- Extra image thumbs --}}
                    @foreach($product->productImages as $img)
                        <img src="{{ Storage::url($img->path) }}"
                            class="product-thumb"
                            onclick="switchImage(this, '{{ Storage::url($img->path) }}')">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── INFO PANEL ── --}}
            <div class="product-detail-info">

                <p class="product-category-label">{{ $product->category->name }}</p>
                <h1 class="product-detail-name">{{ $product->name }}</h1>
                <p class="product-detail-price">₱{{ number_format($product->price, 0) }}</p>

                @if($product->description)
                    <p class="product-detail-desc">{{ $product->description }}</p>
                @endif

                {{-- Stock status --}}
                <div class="product-detail-stock">
                    @if($product->stock > 5)
                        <span class="stock-badge stock-in">✓ In Stock ({{ $product->stock }} available)</span>
                    @elseif($product->stock > 0)
                        <span class="stock-badge stock-low">⚠ Only {{ $product->stock }} left!</span>
                    @else
                        <span class="stock-badge stock-out">✗ Out of Stock</span>
                    @endif
                </div>

                {{-- Add to cart / CTA --}}
                <div class="product-detail-actions">
                    @auth
                        @if($product->stock > 0 && $product->is_available)
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="product-detail-add-btn">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button disabled class="product-detail-add-btn product-detail-add-btn--disabled">
                                Unavailable
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="product-detail-add-btn">
                            Log in to Add to Cart
                        </a>
                    @endauth
                </div>

                {{-- Trust badges --}}
                <div class="product-detail-trust">
                    <div class="trust-item">
                        <span aria-hidden="true">🚚</span>
                        <span>Free delivery over ₱5,000</span>
                    </div>
                    <div class="trust-item">
                        <span aria-hidden="true">🔄</span>
                        <span>365-day returns</span>
                    </div>
                    <div class="trust-item">
                        <span aria-hidden="true">🛡️</span>
                        <span>Secure checkout</span>
                    </div>
                </div>

                <a href="{{ route('shop.index') }}" class="product-detail-back">
                    ← Back to Shop
                </a>

            </div>
        </div>
    </div>
    
    <style>
        .product-thumbnails {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .product-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid var(--ikea-border);
            cursor: pointer;
            transition: border-color .15s;
        }
        .product-thumb:hover { border-color: var(--ikea-blue); }
        .product-thumb.active { border-color: var(--ikea-blue); }
    </style>

    <script>
        function switchImage(thumb, src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>
</x-app-layout>