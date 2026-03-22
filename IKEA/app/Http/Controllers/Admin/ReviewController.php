<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user', 'product')
            ->latest()
            ->get();

        $totalReviews  = Review::count();
        $averageRating = round(Review::avg('rating') ?? 0, 1);
        $fiveStars     = Review::where('rating', 5)->count();

        return view('admin.reviews.index', compact(
            'reviews', 'totalReviews', 'averageRating', 'fiveStars'
        ));
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}