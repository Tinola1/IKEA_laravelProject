<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;

// Before
Route::get('/', function () {
    return view('welcome');
});

// After
use App\Models\Product;
use App\Models\Category;

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/dashboard', function () {
    // Redirect admins and staff straight to the admin dashboard
    if (auth()->user()?->hasAnyRole(['admin', 'staff'])) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Checkout

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // ← ADD THIS
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    
    Route::get('users',                  [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role',    [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::delete('users/{user}',        [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('audit-logs',             [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
});
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');
    });

// Shop - public routes
Route::get('/shop', [ShopProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopProductController::class, 'show'])->name('shop.show');

require __DIR__.'/auth.php';

Route::get('/test-receipt/{order}', function (\App\Models\Order $order) {
    $order->load('items.product', 'user');
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.receipt', compact('order'));
    return $pdf->stream('receipt.pdf');
})->middleware('auth');

Route::get('/orders/{order}/receipt', function (\App\Models\Order $order) {
    // Only allow the order owner
    if ($order->user_id !== auth()->id()) abort(403);

    $order->load('items.product', 'user');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.receipt', compact('order'))
        ->setPaper('a4', 'portrait');

    $filename = 'IKEA-Receipt-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';

    return $pdf->download($filename);
})->name('orders.receipt');