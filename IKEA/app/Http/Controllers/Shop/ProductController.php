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

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_available, 404);
        return view('shop.products.show', compact('product'));
    }
}