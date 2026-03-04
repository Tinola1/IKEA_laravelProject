<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
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

        $order->update([
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Order updated successfully.');
    }
}