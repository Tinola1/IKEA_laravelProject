<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('stock', 'asc')
            ->get();

        $lowStock   = Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $outOfStock = Product::where('stock', 0)->count();

        return view('admin.inventory.index', compact('products', 'lowStock', 'outOfStock'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'         => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        $previousStatus = $order->status;

        $order->update([
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        // ── Send status update email only when status actually changes ──
        if ($previousStatus !== $request->status) {
            $order->load('items.product', 'user');
            Mail::to($order->user->email)->send(new OrderStatusUpdated($order));
        }

        return back()->with('success', 'Order updated successfully.');
    }
}