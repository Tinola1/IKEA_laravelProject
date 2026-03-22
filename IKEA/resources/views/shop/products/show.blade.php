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

    <style>
        /* ── THUMBNAILS ── */
        .product-thumbnails { display:flex; gap:8px; margin-top:12px; flex-wrap:wrap; }
        .product-thumb { width:60px; height:60px; object-fit:cover; border-radius:6px; border:2px solid var(--ikea-border); cursor:pointer; transition:border-color .15s; }
        .product-thumb:hover { border-color:var(--ikea-blue); }
        .product-thumb.active { border-color:var(--ikea-blue); }

        /* ── RATING SUMMARY (inline with title) ── */
        .product-rating-summary { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .product-rating-stars .star-filled { color:#f59e0b; font-size:18px; }
        .product-rating-stars .star-empty  { color:#d1d5db; font-size:18px; }
        .product-rating-score { font-size:16px; font-weight:800; color:var(--ikea-dark); }
        .product-rating-count { font-size:13px; color:var(--ikea-gray); }

        /* ── REVIEWS SECTION ── */
        .reviews-section {
            max-width: 860px;
            margin: var(--space-xl) auto 0;
            padding: 0 var(--space-md) var(--space-xl);
        }

        /* Flash */
        .review-flash {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: var(--text-sm);
            font-weight: 600;
            margin-bottom: var(--space-md);
        }
        .review-flash.success { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }
        .review-flash.error   { background:#ffebee; color:#CC0008; border-left:4px solid #CC0008; }

        /* Header */
        .reviews-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: var(--space-sm);
            margin-bottom: var(--space-md);
            padding-bottom: var(--space-md);
            border-bottom: 2px solid var(--ikea-border);
        }
        .reviews-title { font-size:var(--text-2xl); font-weight:900; color:var(--ikea-dark); letter-spacing:-0.5px; }
        .reviews-avg   { display:flex; align-items:center; gap:12px; margin-top:8px; }
        .reviews-avg-score { font-size:48px; font-weight:900; color:var(--ikea-dark); letter-spacing:-2px; line-height:1; }
        .reviews-avg-stars .star-filled { color:#f59e0b; font-size:20px; }
        .reviews-avg-stars .star-empty  { color:#d1d5db; font-size:20px; }
        .reviews-avg-count { font-size:13px; color:var(--ikea-gray); margin-top:2px; }
        .review-write-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: var(--ikea-yellow);
            color: var(--ikea-dark);
            border: none;
            border-radius: 40px;
            font-size: var(--text-sm);
            font-weight: 700;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: background var(--transition-fast);
        }
        .review-write-btn:hover { background:#f0cc00; }

        /* ── REVIEW FORM ── */
        .review-form {
            background: white;
            border: 1.5px solid var(--ikea-blue);
            border-radius: 10px;
            padding: var(--space-md);
            margin-bottom: var(--space-md);
        }
        .review-form-title { font-size:var(--text-lg); font-weight:800; color:var(--ikea-dark); margin-bottom:var(--space-sm); }
        .review-field { display:flex; flex-direction:column; gap:6px; margin-bottom:var(--space-sm); }
        .review-label { font-size:var(--text-sm); font-weight:700; color:var(--ikea-dark); }
        .review-optional { font-weight:400; color:var(--ikea-gray); }
        .review-input {
            width:100%; height:44px; padding:0 14px;
            border:1.5px solid var(--ikea-border); border-radius:8px;
            font-size:var(--text-base); font-family:'Noto Sans',sans-serif;
            color:var(--ikea-dark); background:white;
            transition:border-color .15s, box-shadow .15s;
        }
        .review-input:focus { outline:none; border-color:var(--ikea-blue); box-shadow:0 0 0 3px rgba(0,88,163,.1); }
        .review-textarea { height:auto; padding:12px 14px; resize:vertical; }
        .review-error    { font-size:var(--text-sm); color:#CC0008; font-weight:600; }

        /* Star picker */
        .star-picker { display:flex; align-items:center; gap:4px; }
        .star-pick {
            font-size:32px; background:none; border:none; cursor:pointer;
            color:#d1d5db; transition:color .1s, transform .1s;
            padding:0; line-height:1;
        }
        .star-pick:hover,
        .star-pick.active { color:#f59e0b; transform:scale(1.15); }
        .star-label { font-size:var(--text-sm); color:var(--ikea-gray); font-weight:600; margin-left:8px; }

        /* Review form buttons */
        .review-submit-btn {
            height:44px; padding:0 24px; background:var(--ikea-yellow); color:var(--ikea-dark);
            border:none; border-radius:40px; font-size:var(--text-base); font-weight:700;
            font-family:'Noto Sans',sans-serif; cursor:pointer; transition:background .15s;
        }
        .review-submit-btn:hover { background:#f0cc00; }
        .review-cancel-btn {
            height:44px; padding:0 24px; background:white; color:var(--ikea-gray);
            border:1.5px solid var(--ikea-border); border-radius:40px; font-size:var(--text-base);
            font-weight:700; font-family:'Noto Sans',sans-serif; cursor:pointer; transition:all .15s;
        }
        .review-cancel-btn:hover { border-color:var(--ikea-dark); color:var(--ikea-dark); }

        /* Own review notice */
        .review-own-notice {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #e3f2fd;
            border: 1.5px solid #90caf9;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: var(--space-md);
            flex-wrap: wrap;
            gap: 10px;
        }
        .review-stars-sm .star-filled { color:#f59e0b; }
        .review-stars-sm .star-empty  { color:#d1d5db; }
        .review-edit-btn {
            padding:6px 14px; background:var(--ikea-blue); color:white;
            border:none; border-radius:6px; font-size:12px; font-weight:700;
            font-family:'Noto Sans',sans-serif; cursor:pointer; transition:background .15s;
        }
        .review-edit-btn:hover { background:#004f94; }
        .review-delete-btn {
            padding:6px 14px; background:#ffebee; color:#CC0008;
            border:1.5px solid #ffcdd2; border-radius:6px; font-size:12px; font-weight:700;
            font-family:'Noto Sans',sans-serif; cursor:pointer; transition:all .15s;
        }
        .review-delete-btn:hover { background:#CC0008; color:white; border-color:#CC0008; }

        /* Login prompt */
        .review-login-prompt {
            background:var(--ikea-light); border-radius:8px; padding:14px 16px;
            font-size:var(--text-sm); color:var(--ikea-gray); margin-bottom:var(--space-md);
        }
        .review-login-prompt a { color:var(--ikea-blue); font-weight:700; }

        /* Empty state */
        .reviews-empty {
            text-align:center; padding:var(--space-xl);
            color:var(--ikea-gray); font-size:var(--text-sm);
            display:flex; flex-direction:column; align-items:center; gap:8px;
        }

        /* ── REVIEW CARDS ── */
        .reviews-list { display:flex; flex-direction:column; gap:var(--space-sm); }
        .review-card {
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 10px;
            padding: var(--space-md);
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .review-card-own { border-color:#90caf9; background:#f5f9ff; }
        .review-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .review-card-user { display:flex; align-items:center; gap:10px; }
        .review-avatar {
            width:36px; height:36px; background:var(--ikea-blue); color:white;
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:15px; font-weight:900; flex-shrink:0;
        }
        .review-username { font-size:var(--text-base); font-weight:700; color:var(--ikea-dark); display:flex; align-items:center; gap:6px; }
        .review-you-badge {
            font-size:10px; font-weight:800; background:var(--ikea-blue); color:white;
            padding:2px 7px; border-radius:40px; letter-spacing:.5px; text-transform:uppercase;
        }
        .review-date { font-size:12px; color:var(--ikea-gray); }
        .review-card-stars .star-filled { color:#f59e0b; font-size:16px; }
        .review-card-stars .star-empty  { color:#d1d5db; font-size:16px; }
        .review-card-title { font-size:var(--text-base); font-weight:800; color:var(--ikea-dark); margin-bottom:6px; }
        .review-card-body  { font-size:var(--text-sm); color:#444; line-height:1.7; }
    </style>

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