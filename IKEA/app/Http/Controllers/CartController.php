<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if (!$product->is_available || $product->stock < 1) {
            return back()->with('error', 'This product is not available.');
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + 1;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Not enough stock available.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        return back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) abort(403);

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($request->quantity > $cart->product->stock) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) abort(403);

        $cart->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}