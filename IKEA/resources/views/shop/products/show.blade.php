@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>
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

               
            </div>

            {{-- ── INFO PANEL ── --}}
            <div class="product-detail-info">

                <p class="product-category-label">{{ $product->category->name }}</p>
                <h1 class="product-detail-name">{{ $product->name }}</h1>

                {{-- Rating summary inline --}}
                @php
                    $avgRating    = $product->averageRating();
                    $reviewCount  = $product->reviews()->count();
                @endphp
                @if($reviewCount > 0)
                    <div class="product-rating-summary">
                        <span class="product-rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= round($avgRating) ? 'star-filled' : 'star-empty' }}">★</span>
                            @endfor
                        </span>
                        <span class="product-rating-score">{{ $avgRating }}</span>
                        <span class="product-rating-count">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </div>
                @endif

                <p class="product-detail-price">₱{{ number_format($product->price, 0) }}</p>

                @if($product->description)
                    <p class="product-detail-desc">{{ $product->description }}</p>
                @endif

                <div class="product-detail-stock">
                    @if($product->stock > 5)
                        <span class="stock-badge stock-in">✓ In Stock ({{ $product->stock }} available)</span>
                    @elseif($product->stock > 0)
                        <span class="stock-badge stock-low">⚠ Only {{ $product->stock }} left!</span>
                    @else
                        <span class="stock-badge stock-out">✗ Out of Stock</span>
                    @endif
                </div>

                <div class="product-detail-actions">
                    @auth
                        @if($product->stock > 0 && $product->is_available)
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="product-detail-add-btn">Add to Cart</button>
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

                <div class="product-detail-trust">
                    <div class="trust-item"><span>🚚</span><span>Free delivery over ₱5,000</span></div>
                    <div class="trust-item"><span>🔄</span><span>365-day returns</span></div>
                    <div class="trust-item"><span>🛡️</span><span>Secure checkout</span></div>
                </div>

                <a href="{{ route('shop.index') }}" class="product-detail-back">← Back to Shop</a>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             REVIEWS SECTION
        ════════════════════════════════════════════════════════ --}}
        <div class="reviews-section" id="reviews">

            {{-- Flash messages --}}
            @if(session('review_success'))
                <div class="review-flash success">✅ {{ session('review_success') }}</div>
            @endif
            @if(session('review_error'))
                <div class="review-flash error">❌ {{ session('review_error') }}</div>
            @endif

            {{-- ── REVIEWS HEADER ── --}}
            <div class="reviews-header">
                <div>
                    <h2 class="reviews-title">Customer Reviews</h2>
                    @if($reviewCount > 0)
                        <div class="reviews-avg">
                            <span class="reviews-avg-score">{{ $avgRating }}</span>
                            <div>
                                <div class="reviews-avg-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= round($avgRating) ? 'star-filled' : 'star-empty' }}">★</span>
                                    @endfor
                                </div>
                                <div class="reviews-avg-count">Based on {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Write review button --}}
                @auth
                    @php
                        $userReview   = $product->reviews()->where('user_id', auth()->id())->first();
                        $hasPurchased = auth()->user()
                            ->orders()
                            ->whereIn('status', ['completed', 'processing'])
                            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                            ->exists();
                    @endphp
                    @if(!$userReview && $hasPurchased)
                        <button onclick="document.getElementById('writeReviewForm').style.display='block';this.style.display='none';"
                                class="review-write-btn">
                            ✍️ Write a Review
                        </button>
                    @endif
                @endauth
            </div>

            {{-- ── WRITE REVIEW FORM ── --}}
            @auth
                @if($hasPurchased && !$userReview)
                    <div id="writeReviewForm" style="display:none;">
                        <form method="POST"
                              action="{{ route('reviews.store', $product) }}"
                              class="review-form">
                            @csrf
                            <h3 class="review-form-title">Write Your Review</h3>

                            {{-- Star rating --}}
                            <div class="review-field">
                                <label class="review-label">Your Rating <span style="color:#CC0008;">*</span></label>
                                <div class="star-picker" id="starPicker">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                class="star-pick"
                                                data-val="{{ $i }}"
                                                onclick="pickStar({{ $i }})">★</button>
                                    @endfor
                                    <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating') }}" required>
                                    <span id="starLabel" class="star-label">Click to rate</span>
                                </div>
                                @error('rating')<span class="review-error">{{ $message }}</span>@enderror
                            </div>

                            {{-- Title --}}
                            <div class="review-field">
                                <label class="review-label" for="review_title">Review Title <span class="review-optional">(optional)</span></label>
                                <input type="text" id="review_title" name="title"
                                       class="review-input"
                                       value="{{ old('title') }}"
                                       placeholder="Summarise your experience in a few words">
                                @error('title')<span class="review-error">{{ $message }}</span>@enderror
                            </div>

                            {{-- Body --}}
                            <div class="review-field">
                                <label class="review-label" for="review_body">Your Review <span class="review-optional">(optional)</span></label>
                                <textarea id="review_body" name="body"
                                          class="review-input review-textarea"
                                          rows="4"
                                          placeholder="What did you like or dislike? How does it fit in your home?">{{ old('body') }}</textarea>
                                @error('body')<span class="review-error">{{ $message }}</span>@enderror
                            </div>

                            <div style="display:flex;gap:10px;align-items:center;">
                                <button type="submit" class="review-submit-btn">Submit Review</button>
                                <button type="button"
                                        onclick="document.getElementById('writeReviewForm').style.display='none';document.querySelector('.review-write-btn').style.display='inline-flex';"
                                        class="review-cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- ── EDIT OWN REVIEW ── --}}
                @if($userReview)
                    <div class="review-own-notice">
                        <div>
                            <strong>Your review</strong>
                            <span class="review-stars-sm">
                                @for($i=1;$i<=5;$i++)
                                    <span class="{{ $i <= $userReview->rating ? 'star-filled' : 'star-empty' }}">★</span>
                                @endfor
                            </span>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button onclick="toggleEditForm()" class="review-edit-btn">✏️ Edit</button>
                            <form method="POST" action="{{ route('reviews.destroy', [$product, $userReview]) }}"
                                  onsubmit="return confirm('Delete your review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="review-delete-btn">🗑 Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit form --}}
                    <div id="editReviewForm" style="display:none;">
                        <form method="POST"
                              action="{{ route('reviews.update', [$product, $userReview]) }}"
                              class="review-form">
                            @csrf
                            @method('PATCH')
                            <h3 class="review-form-title">Edit Your Review</h3>

                            <div class="review-field">
                                <label class="review-label">Rating</label>
                                <div class="star-picker" id="starPickerEdit">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                class="star-pick {{ $i <= $userReview->rating ? 'active' : '' }}"
                                                data-val="{{ $i }}"
                                                onclick="pickStarEdit({{ $i }})">★</button>
                                    @endfor
                                    <input type="hidden" name="rating" id="ratingInputEdit" value="{{ $userReview->rating }}" required>
                                </div>
                            </div>

                            <div class="review-field">
                                <label class="review-label">Title</label>
                                <input type="text" name="title" class="review-input"
                                       value="{{ old('title', $userReview->title) }}">
                            </div>

                            <div class="review-field">
                                <label class="review-label">Review</label>
                                <textarea name="body" class="review-input review-textarea" rows="4">{{ old('body', $userReview->body) }}</textarea>
                            </div>

                            <div style="display:flex;gap:10px;">
                                <button type="submit" class="review-submit-btn">Save Changes</button>
                                <button type="button" onclick="toggleEditForm()" class="review-cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                @endif

            @else
                <div class="review-login-prompt">
                    <a href="{{ route('login') }}">Log in</a> to write a review.
                    Only verified purchasers can leave reviews.
                </div>
            @endauth

            {{-- ── REVIEWS LIST ── --}}
            @php $reviews = $product->reviews()->with('user')->get(); @endphp

            @if($reviews->isEmpty())
                <div class="reviews-empty">
                    <span style="font-size:40px;">⭐</span>
                    <p>No reviews yet. Be the first to share your experience!</p>
                </div>
            @else
                <div class="reviews-list">
                    @foreach($reviews as $review)
                        <div class="review-card {{ $review->user_id === auth()->id() ? 'review-card-own' : '' }}">
                            <div class="review-card-header">
                                <div class="review-card-user">
                                    <div class="review-avatar">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="review-username">
                                            {{ $review->user->name }}
                                            @if($review->user_id === auth()->id())
                                                <span class="review-you-badge">You</span>
                                            @endif
                                        </div>
                                        <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="review-card-stars">
                                    @for($i=1;$i<=5;$i++)
                                        <span class="{{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}">★</span>
                                    @endfor
                                </div>
                            </div>

                            @if($review->title)
                                <div class="review-card-title">{{ $review->title }}</div>
                            @endif

                            @if($review->body)
                                <p class="review-card-body">{{ $review->body }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>{{-- /reviews-section --}}
    </div>

    <script>
        // Image switcher
        function switchImage(thumb, src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }

        // Star picker — write form
        const starLabels = ['','Terrible','Poor','Okay','Good','Excellent'];
        function pickStar(val) {
            document.getElementById('ratingInput').value = val;
            document.querySelectorAll('#starPicker .star-pick').forEach((s, i) => {
                s.classList.toggle('active', i < val);
            });
            const el = document.getElementById('starLabel');
            if (el) el.textContent = starLabels[val];
        }

        // Star picker — edit form
        function pickStarEdit(val) {
            document.getElementById('ratingInputEdit').value = val;
            document.querySelectorAll('#starPickerEdit .star-pick').forEach((s, i) => {
                s.classList.toggle('active', i < val);
            });
        }

        // Toggle edit form
        function toggleEditForm() {
            const f = document.getElementById('editReviewForm');
            f.style.display = f.style.display === 'none' ? 'block' : 'none';
        }

        // Highlight stars on hover
        document.querySelectorAll('.star-picker').forEach(picker => {
            const stars = picker.querySelectorAll('.star-pick');
            stars.forEach((star, idx) => {
                star.addEventListener('mouseenter', () => {
                    stars.forEach((s, i) => s.style.color = i <= idx ? '#f59e0b' : '#d1d5db');
                });
                star.addEventListener('mouseleave', () => {
                    stars.forEach(s => s.style.color = '');
                });
            });
        });
    </script>
</x-app-layout>