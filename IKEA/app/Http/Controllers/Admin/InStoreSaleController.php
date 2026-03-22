<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InStoreSaleController extends Controller
{
    public function create()
    {
        $products  = Product::with('category')
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $customers = User::whereHas('roles', fn($q) => $q->where('name', 'customer'))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'province'       => 'nullable|string|max:100',
            'zip_code'       => 'nullable|string|max:10',
        ]);

        $order = null;

        DB::transaction(function () use ($request, &$order) {
            $total = 0;
            $items = [];

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                $qty     = (int) $item['qty'];

                if ($product->stock < $qty) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }

                $subtotal = $product->price * $qty;
                $total   += $subtotal;

                $items[] = [
                    'product'  => $product,
                    'quantity' => $qty,
                    'price'    => $product->price,
                ];
            }

            $order = Order::create([
                'user_id'        => $request->user_id ?? auth()->id(),
                'status'         => 'processing', // in-store = immediately processing
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',        // in-store = paid on the spot
                'full_name'      => $request->full_name,
                'phone'          => $request->phone,
                'address'        => 'In-Store Purchase',
                'city'           => '—',
                'province'       => '—',
                'zip_code'       => '—',
                'notes'          => '[IN-STORE SALE] ' . $request->notes,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);

                if ($item['product']->stock <= 0) {
                    $item['product']->update(['is_available' => false]);
                }
            }
        });

        // Send email receipt if email provided
        if ($order && $request->email) {
            try {
                $order->load('items.product', 'user');
                Mail::to($request->email)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                \Log::error('In-store sale email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'In-store sale recorded successfully. Order #' . $order->id . ' created.');
    }
}