<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div class="shop-page-header">
            <div>
                <h2 class="shop-page-title">My Account</h2>
                <p class="shop-page-subtitle">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
        </div>
    </x-slot>

    <div class="customer-dashboard">
        <div class="customer-card">
            <div class="customer-dashboard-welcome">👋 Hello, {{ auth()->user()->name }}!</div>
            <p style="color:var(--ikea-gray);font-size:var(--text-sm);margin-top:6px;">
                What would you like to do today?
            </p>
        </div>

        <div class="customer-dashboard-links">
            <a href="{{ route('shop.index') }}" class="customer-dashboard-link">
                <span class="customer-dashboard-link-icon">🛋️</span>
                <div>
                    <div>Browse Shop</div>
                    <div class="customer-dashboard-link-desc">Explore our furniture and home furnishings</div>
                </div>
            </a>
            <a href="{{ route('orders.index') }}" class="customer-dashboard-link">
                <span class="customer-dashboard-link-icon">📦</span>
                <div>
                    <div>My Orders</div>
                    <div class="customer-dashboard-link-desc">Track and manage your orders</div>
                </div>
            </a>
            <a href="{{ route('cart.index') }}" class="customer-dashboard-link">
                <span class="customer-dashboard-link-icon">🛒</span>
                <div>
                    <div>My Cart</div>
                    <div class="customer-dashboard-link-desc">View items in your cart</div>
                </div>
            </a>
            <a href="{{ route('appointments.index') }}" class="customer-dashboard-link">
                <span class="customer-dashboard-link-icon">📅</span>
                <div>
                    <div>My Appointments</div>
                    <div class="customer-dashboard-link-desc">Manage your showroom appointments</div>
                </div>
            </a>
            <a href="{{ route('profile.edit') }}" class="customer-dashboard-link">
                <span class="customer-dashboard-link-icon">👤</span>
                <div>
                    <div>Profile Settings</div>
                    <div class="customer-dashboard-link-desc">Update your account, address, and preferences</div>
                </div>
            </a>
        </div>
    </div>

</x-app-layout>