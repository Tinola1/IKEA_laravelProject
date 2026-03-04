<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'zip_code'       => 'required|string|max:10',
            'payment_method' => 'required|in:cod,gcash,bank_transfer',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        DB::transaction(function () use ($request, $cartItems, $total) {
            // Create the order
            $order = Order::create([
                'user_id'        => Auth::id(),
                'status'         => 'pending',
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'full_name'      => $request->full_name,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'city'           => $request->city,
                'province'       => $request->province,
                'zip_code'       => $request->zip_code,
                'notes'          => $request->notes,
            ]);

            // Create order items and reduce stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                ]);

                // Reduce stock
                $item->product->decrement('stock', $item->quantity);

                // Mark unavailable if out of stock
                if ($item->product->stock <= 0) {
                    $item->product->update(['is_available' => false]);
                }
            }

            // Clear the cart
            Cart::where('user_id', Auth::id())->delete();

            session(['last_order_id' => $order->id]);
        });

        return redirect()->route('checkout.success');
    }

    public function success()
    {
        $orderId = session('last_order_id');

        if (!$orderId) {
            return redirect()->route('shop.index');
        }

        $order = Order::with('items.product')->findOrFail($orderId);

        return view('checkout.success', compact('order'));
    }
}