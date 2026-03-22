<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        // Home page search using Laravel Scout
        if ($request->filled('search')) {
            $searchResults = Product::search($request->search)
                ->where('is_available', true)
                ->paginate(12)
                ->withQueryString();

            return view('welcome', [
                'featuredProducts' => collect(),
                'categories'       => $categories,
                'searchResults'    => $searchResults,
                'searchQuery'      => $request->search,
            ]);
        }

        $featuredProducts = Product::with('category')
            ->where('is_available', true)
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('featuredProducts', 'categories'));
    }
}