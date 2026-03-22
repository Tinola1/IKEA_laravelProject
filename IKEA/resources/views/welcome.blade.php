<x-app-layout>
    <x-slot name="title">Furniture &amp; Home Furnishings</x-slot>

    {{-- ── MAIN ── --}}
    <main id="main-content">

        {{-- ── HERO ── --}}
        <section class="hero" aria-label="Hero banner">
            <div class="hero-content">
                <span class="hero-tag">New Collection 2025</span>
                <h1>Beautiful homes<br>start <span>here.</span></h1>
                <p>Discover furniture and home furnishings that make everyday life better — designed for real life at prices that make sense.</p>
                <div class="hero-cta">
                    <a href="{{ route('shop.index') }}" class="cta-main">Shop Now</a>
                    <a href="#" class="cta-outline">View Catalogue</a>
                </div>
            </div>

            {{-- CSS room illustration --}}
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-visual-inner">
                    <div class="room-scene">
                        <div class="wall"></div>
                        <div class="floor"></div>
                        <div class="lamp">
                            <div class="lamp-shade"></div>
                            <div class="lamp-pole"></div>
                            <div class="lamp-base"></div>
                        </div>
                        <div class="plant">
                            <div class="plant-leaves"></div>
                            <div class="plant-pot"></div>
                        </div>
                        <div class="table">
                            <div class="table-top"></div>
                            <div class="table-legs">
                                <div class="table-leg"></div>
                                <div class="table-leg"></div>
                            </div>
                        </div>
                        <div class="sofa">
                            <div class="sofa-back"></div>
                            <div class="sofa-seat">
                                <div class="sofa-leg left"></div>
                                <div class="sofa-leg right"></div>
                            </div>
                        </div>
                        <div class="price-tag" aria-hidden="true">
                            <span class="from">From</span>
                            <span class="amount">₱999</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── CATEGORY CHIPS ── --}}
        @php
            $categoryEmojis = [
                'sofas-armchairs' => '🛋️',
                'beds-mattresses' => '🛏️',
                'tables-desks'    => '🪑',
                'chairs'          => '🪑',
                'kitchen-dining'  => '🍳',
                'default'         => '🏠',
            ];
        @endphp

        <nav class="categories-strip" aria-label="Shop by category">
            <h2>Shop by category</h2>
            <div class="categories-grid" role="list">
                <a href="{{ route('shop.index') }}" class="category-chip active" role="listitem">
                    <span class="icon" aria-hidden="true">🏠</span> All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('shop.index') }}?category={{ $category->slug }}"
                       class="category-chip"
                       role="listitem">
                        <span class="icon" aria-hidden="true">
                            {{ $categoryEmojis[$category->slug] ?? $categoryEmojis['default'] }}
                        </span>
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </nav>

        {{-- ── FEATURES BAR ── --}}
        <div class="features" role="list" aria-label="Store features">
            <div class="feature" role="listitem">
                <div class="feature-icon" aria-hidden="true">🚚</div>
                <div>
                    <h3>Free Delivery</h3>
                    <p>On all orders over ₱5,000. Fast and reliable delivery to your door.</p>
                </div>
            </div>
            <div class="feature" role="listitem">
                <div class="feature-icon" aria-hidden="true">🔄</div>
                <div>
                    <h3>365-Day Returns</h3>
                    <p>Not happy? Return it within a year for a full refund, no questions asked.</p>
                </div>
            </div>
            <div class="feature" role="listitem">
                <div class="feature-icon" aria-hidden="true">🏪</div>
                <div>
                    <h3>Showroom Experience</h3>
                    <p>Book an appointment and see our full range in person at our store.</p>
                </div>
            </div>
        </div>

        {{-- ── PROMO BANNERS ── --}}
        <section aria-label="Promotions">
            <div class="promo-grid">
                <a href="{{ route('shop.index') }}?search=&category=1" class="promo-card blue">
                    <div class="promo-bg-shape" aria-hidden="true"></div>
                    <div class="label">Limited Time Offer</div>
                    <h3>Up to 30% off<br>Living Room<br>Essentials</h3>
                    <span class="link">Shop the sale →</span>
                </a>
                <div class="promo-stack">
                    <a href="{{ route('shop.index') }}?category=beds-mattresses" class="promo-card dark">
                        <div class="promo-bg-shape" aria-hidden="true"></div>
                        <div class="label">New Arrivals</div>
                        <h3>Bedroom<br>Collection</h3>
                        <span class="link">Explore now →</span>
                    </a>
<a href="{{ route('appointments.create') }}" class="promo-card yellow">                      
      <div class="promo-bg-shape" style="background:#000;opacity:0.05;" aria-hidden="true"></div>
                        <div class="label" style="color:rgba(0,0,0,0.5);">Book Today</div>
                        <h3>Visit Our<br>Showroom</h3>
                        <span class="link">Book appointment →</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- ── POPULAR PRODUCTS ── --}}
        @php
            $productEmojis = [
                1 => '🛋️',
                2 => '🛏️',
                3 => '🪑',
                4 => '🪑',
                5 => '🍳',
            ];
            $newestIds = $featuredProducts->sortByDesc('created_at')->take(3)->pluck('id');
        @endphp

        <section class="section" aria-label="Popular products">
            <div class="section-header">
                <h2>Popular right now</h2>
                <a href="{{ route('shop.index') }}" aria-label="See all products">See all products →</a>
            </div>

            <div class="products-grid">
                @forelse($featuredProducts as $product)
                    @php
                        if ($product->stock <= 5 && $product->stock > 0) {
                            $badge = 'Low Stock'; $badgeClass = 'sale';
                        } elseif ($product->stock === 0) {
                            $badge = 'Out of Stock'; $badgeClass = 'sale';
                        } elseif ($newestIds->contains($product->id)) {
                            $badge = 'New'; $badgeClass = 'new';
                        } elseif ($product->price < 5000) {
                            $badge = 'Great Value'; $badgeClass = '';
                        } else {
                            $badge = null; $badgeClass = '';
                        }
                        $emoji   = $productEmojis[$product->category_id] ?? '🏠';
                        $inStock = $product->is_available && $product->stock > 0;
                    @endphp

                    <a href="{{ route('shop.show', $product) }}" class="product-card">
                        <div class="product-img">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;"
                                     loading="lazy">
                            @else
                                <span aria-hidden="true">{{ $emoji }}</span>
                            @endif
                            @if($badge)
                                <span class="product-badge {{ $badgeClass }}">{{ $badge }}</span>
                            @endif
                        </div>
                        <div class="product-info">
                            <div class="name">{{ $product->name }}</div>
                            <div class="desc desc-clamp">{{ $product->description }}</div>
                            <div>
                                <span class="price">₱{{ number_format($product->price, 0) }}</span>
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span class="stock-warning" style="margin-left:8px;">
                                        Only {{ $product->stock }} left!
                                    </span>
                                @endif
                            </div>
                        </div>
                        <button class="add-btn"
                                aria-label="Add {{ $product->name }} to cart"
                                @unless($inStock) disabled @endunless
                                @unless($inStock) style="opacity:0.5;cursor:not-allowed;background:#ccc;" @endunless>
                            {{ $inStock ? 'View Product' : 'Out of Stock' }}
                        </button>
                    </a>
                @empty
                    <div style="grid-column:1/-1;padding:48px;text-align:center;color:var(--ikea-gray);">
                        No products available right now. Check back soon!
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ── MEMBERSHIP ── --}}
        <section class="membership" aria-label="IKEA Family membership">
            <div>
                <h2>Join <span>IKEA Family</span><br>— it's free.</h2>
                <p>Get exclusive member discounts, early access to sales, free design services, and more. Over 200 million members worldwide can't be wrong.</p>
                <div class="membership-perks" role="list">
                    <div class="perk" role="listitem">
                        <div class="check" aria-hidden="true">✓</div>
                        <span>Member prices</span>
                    </div>
                    <div class="perk" role="listitem">
                        <div class="check" aria-hidden="true">✓</div>
                        <span>Free design help</span>
                    </div>
                    <div class="perk" role="listitem">
                        <div class="check" aria-hidden="true">✓</div>
                        <span>Early sale access</span>
                    </div>
                </div>
            </div>
            <div class="membership-actions">
                @guest
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-yellow">Create free account</a>
                    @endif
                    <span class="btn-ghost-white">
                        Already a member? <a href="{{ route('login') }}">Log in</a>
                    </span>
                @else
                    <a href="{{ url('/dashboard') }}" class="btn-yellow">Go to Dashboard</a>
                @endguest
            </div>
        </section>

    </main>

</x-app-layout>