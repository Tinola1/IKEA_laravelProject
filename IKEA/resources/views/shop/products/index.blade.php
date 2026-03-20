@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Shop</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">All Products</h2>
                <p class="shop-page-subtitle">
                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }} found
                </p>
            </div>
        </div>
    </x-slot>

    <div class="shop-page">

        {{-- ── FILTER BAR ── --}}
        <form method="GET" action="{{ route('shop.index') }}" class="shop-filter-bar">

            <div class="shop-filter-inner">
                <div class="filter-search-wrap">
                    <span class="filter-search-icon" aria-hidden="true">🔍</span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products…"
                        class="filter-search-input"
                        aria-label="Search products"
                    >
                </div>

                <select name="category" class="filter-select" aria-label="Filter by category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <div class="filter-actions">
                    <button type="submit" class="filter-btn-apply">Apply</button>
                    <a href="{{ route('shop.index') }}" class="filter-btn-reset">Reset</a>
                </div>
            </div>

            {{-- Active filter indicator --}}
            @if(request('search') || request('category'))
                <div class="filter-active-bar">
                    <span class="filter-active-label">Filtering by:</span>
                    @if(request('search'))
                        <span class="filter-tag">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('shop.index', array_merge(request()->except('search', 'page'))) }}" aria-label="Remove search filter">×</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="filter-tag">
                            Category: {{ $categories->firstWhere('id', request('category'))?->name }}
                            <a href="{{ route('shop.index', array_merge(request()->except('category', 'page'))) }}" aria-label="Remove category filter">×</a>
                        </span>
                    @endif
                </div>
            @endif

        </form>

        {{-- ── PRODUCT GRID ── --}}
        <div class="shop-grid-wrapper">
            <div class="products-grid shop-products-grid">
                @forelse($products as $product)
                    @php
                        $inStock = $product->stock > 0 && $product->is_available;
                        if ($product->stock === 0 || !$product->is_available) {
                            $badge = 'Out of Stock'; $badgeClass = 'sale';
                        } elseif ($product->stock <= 5) {
                            $badge = 'Low Stock'; $badgeClass = 'sale';
                        } elseif ($product->price < 5000) {
                            $badge = 'Great Value'; $badgeClass = 'new';
                        } else {
                            $badge = null; $badgeClass = '';
                        }
                    @endphp

                    <a href="{{ route('shop.show', $product) }}" class="product-card">

                        <div class="product-img">
                            @if($product->image)
                                <img
                                    src="{{ Storage::url($product->image) }}"
                                    alt="{{ $product->name }}"
                                    style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;"
                                    loading="lazy"
                                >
                            @else
                                <span aria-hidden="true" class="product-img-placeholder">
                                    {{ ['🛋️','🛏️','🪑','🍳','🪞','💡','🪴','🛁'][($product->id - 1) % 8] }}
                                </span>
                            @endif

                            @if($badge)
                                <span class="product-badge {{ $badgeClass }}">{{ $badge }}</span>
                            @endif
                        </div>

                        <div class="product-info">
                            <div class="product-category-label">{{ $product->category->name }}</div>
                            <div class="name">{{ $product->name }}</div>
                            <div class="desc desc-clamp">{{ $product->description }}</div>
                            <div class="product-price-row">
                                <span class="price">₱{{ number_format($product->price, 0) }}</span>
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span class="stock-warning">Only {{ $product->stock }} left</span>
                                @endif
                            </div>
                        </div>

                        <div class="add-btn {{ !$inStock ? 'add-btn-disabled' : '' }}">
                            {{ $inStock ? 'View Product' : 'Out of Stock' }}
                        </div>

                    </a>

                @empty
                    <div class="shop-empty">
                        <div class="shop-empty-icon" aria-hidden="true">🔍</div>
                        <h3>No products found</h3>
                        <p>Try adjusting your search or filter to find what you're looking for.</p>
                        <a href="{{ route('shop.index') }}" class="cta-main" style="display:inline-block;margin-top:16px;">
                            Clear filters
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>