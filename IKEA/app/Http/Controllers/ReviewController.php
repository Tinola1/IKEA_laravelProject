<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     * Only allowed if the customer has a completed/processing order containing this product.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:150',
            'body'   => 'nullable|string|max:2000',
        ]);

        // Verify purchase
        $hasPurchased = Auth::user()
            ->orders()
            ->whereIn('status', ['completed', 'processing'])
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->exists();

        if (!$hasPurchased) {
            return back()->with('review_error', 'You can only review products you have purchased.');
        }

        // Prevent duplicate review
        if (Review::where('user_id', Auth::id())->where('product_id', $product->id)->exists()) {
            return back()->with('review_error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
            'rating'     => $request->rating,
            'title'      => $request->title,
            'body'       => $request->body,
        ]);

        return back()->with('review_success', 'Thank you for your review!');
    }

    /**
     * Show edit form — returns to the product page with edit state.
     */
    public function edit(Product $product, Review $review)
    {
        if ($review->user_id !== Auth::id()) abort(403);
        return redirect()->route('shop.show', $product)->withFragment('reviews');
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, Product $product, Review $review)
    {
        if ($review->user_id !== Auth::id()) abort(403);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:150',
            'body'   => 'nullable|string|max:2000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'title'  => $request->title,
            'body'   => $request->body,
        ]);

        return back()->with('review_success', 'Your review has been updated.');
    }

    /**
     * Delete own review.
     */
    public function destroy(Product $product, Review $review)
    {
        if ($review->user_id !== Auth::id()) abort(403);

        $review->delete();

        return back()->with('review_success', 'Your review has been removed.');
    }
}