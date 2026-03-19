<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products   = Product::with('category')->orderBy('stock', 'asc')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $lowStock   = Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $outOfStock = Product::where('stock', 0)->count();

        return view('admin.inventory.index', compact('products', 'categories', 'lowStock', 'outOfStock'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'stock'        => $request->stock,
            'is_available' => $request->stock > 0,
        ]);

        return back()->with('success', "Stock for {$product->name} updated successfully.");
    }
}