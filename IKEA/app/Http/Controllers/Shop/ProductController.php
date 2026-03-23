<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_available', true);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Sort
        match($request->sort ?? '') {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(22)->withQueryString();
        $categories = Category::all();
        $minPrice   = Product::where('is_available', true)->min('price') ?? 0;
        $maxPrice   = Product::where('is_available', true)->max('price') ?? 100000;

        return view('shop.products.index', compact(
            'products', 'categories', 'minPrice', 'maxPrice'
        ));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_available, 404);
        $product->load('category', 'productImages');
        return view('shop.products.show', compact('product'));
    }
}