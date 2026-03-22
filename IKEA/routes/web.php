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
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\InStoreSaleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;


// ── Public routes ────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopProductController::class, 'show'])->name('shop.show');

// ── Auth routes ──────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Dashboard (redirects admins to admin panel) ──────────────────
Route::get('/dashboard', function () {
    if (auth()->user()?->hasAnyRole(['admin', 'staff'])) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Authenticated user routes ────────────────────────────────────
Route::middleware(['auth'])->group(function () {

// For sake of testing, change to below to switch back to AUTHENTICATION
// Route::middleware(['auth', 'verified'])->group(function () {

// Reviews (customer)
Route::post('/shop/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::patch('/shop/{product}/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/shop/{product}/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');


    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Receipt download
    Route::get('/orders/{order}/receipt', function (\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('items.product', 'user');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.receipt', compact('order'))
            ->setPaper('a4', 'portrait');
        $filename = 'IKEA-Receipt-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($filename);
    })->name('orders.receipt');

    // ── Showroom Appointments (customer) ─────────────────────────
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/book', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

});

// ── Unauthenticated /admin route ─────────────────────────────────
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

// ── Admin routes ─────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|staff'])->group(function () {

    // Reviews (admin)
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::post('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Products
    Route::post('products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
    Route::post('products/preview', [ProductController::class, 'import'])->name('products.import');
    Route::post('products/confirm', [ProductController::class, 'confirmImport'])->name('products.confirm');
    Route::get('products/template', [ProductController::class, 'downloadTemplate'])->name('products.template');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');

    // Users
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::patch('users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Audit logs
    Route::get('audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

    // ── Showroom Appointments (admin/staff) ───────────────────────
    Route::get('appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

    // ── In-Store Sales ────────────────────────────────────────────
    Route::get('sales/create', [InStoreSaleController::class, 'create'])->name('sales.create');
    Route::post('sales', [InStoreSaleController::class, 'store'])->name('sales.store');

});

// ── Dev/test routes ──────────────────────────────────────────────
Route::get('/test-receipt/{order}', function (\App\Models\Order $order) {
    $order->load('items.product', 'user');
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.receipt', compact('order'));
    return $pdf->stream('receipt.pdf');
})->middleware('auth');