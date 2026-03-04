<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
            if ($item->product->stock > 0) {
                $item->product->update(['is_available' => true]);
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully.');
    }
}