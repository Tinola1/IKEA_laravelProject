<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders       = Order::with('user')->latest()->get();
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])->sum('total');
        $statusCounts = Order::selectRaw('status, count(*) as total')
                            ->groupBy('status')->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'totalRevenue', 'statusCounts'));
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