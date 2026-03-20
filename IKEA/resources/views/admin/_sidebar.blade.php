<aside class="admin-sidebar" id="adminSidebar">

    <div class="admin-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-logo">
            <div class="logo-box" style="font-size:18px;padding:4px 10px;">IKEA</div>
            <span>Admin</span>
        </a>
        <button class="admin-sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">✕</button>
    </div>

    <nav class="admin-sidebar-nav" aria-label="Admin navigation">

        <div class="admin-nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}"
           class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="admin-nav-icon">📊</span>
            <span>Dashboard</span>
        </a>

        <div class="admin-nav-section-label">Catalogue</div>
        <a href="{{ route('admin.products.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">🛋️</span>
            <span>Products</span>
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">🏷️</span>
            <span>Categories</span>
        </a>
        <a href="{{ route('admin.inventory.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">📦</span>
            <span>Inventory</span>
            @php $outOfStock = \App\Models\Product::where('stock', '<=', 5)->count(); @endphp
            @if($outOfStock > 0)
                <span class="admin-nav-badge">{{ $outOfStock }}</span>
            @endif
        </a>

        <div class="admin-nav-section-label">Sales</div>
        <a href="{{ route('admin.orders.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">🧾</span>
            <span>Orders</span>
            @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
            @if($pendingOrders > 0)
                <span class="admin-nav-badge">{{ $pendingOrders }}</span>
            @endif
        </a>

        <div class="admin-nav-section-label">People</div>
        <a href="{{ route('admin.users.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">👥</span>
            <span>Users</span>
        </a>

        <div class="admin-nav-section-label">System</div>
        <a href="{{ route('admin.audit-logs.index') }}"
           class="admin-nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
            <span class="admin-nav-icon">📋</span>
            <span>Audit Logs</span>
        </a>
        <div class="admin-nav-section-label">Storefront</div>
        <a href="{{ route('home') }}"
        class="admin-nav-item">
            <span class="admin-nav-icon">🏠</span>
            <span>Back to Store</span>
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <div class="admin-sidebar-user">
            <div class="admin-sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="admin-sidebar-user-info">
                <div class="admin-sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="admin-sidebar-user-role">
                    {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Admin') }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="admin-sidebar-logout">Log out</button>
        </form>
    </div>

</aside>

{{-- Overlay for mobile --}}
<div class="admin-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<style>
    /* ── SIDEBAR ──────────────────────────────────────────── */
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 240px;
        height: 100vh;
        background: #0a0a0a;
        display: flex;
        flex-direction: column;
        z-index: 100;
        transition: transform 0.25s ease;
    }

    .admin-sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 99;
    }

    /* Header */
    .admin-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .admin-sidebar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .admin-sidebar-logo span {
        color: rgba(255,255,255,0.6);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .admin-sidebar-close {
        display: none;
        background: none;
        border: none;
        color: rgba(255,255,255,0.4);
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
    }

    /* Nav */
    .admin-sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: 12px 10px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }
    .admin-nav-section-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.25);
        padding: 14px 8px 6px;
    }
    .admin-nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 7px;
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.15s ease;
        margin-bottom: 2px;
        position: relative;
    }
    .admin-nav-item:hover {
        background: rgba(255,255,255,0.07);
        color: white;
    }
    .admin-nav-item.active {
        background: var(--ikea-blue);
        color: white;
    }
    .admin-nav-icon { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
    .admin-nav-badge {
        margin-left: auto;
        background: #CC0008;
        color: white;
        font-size: 10px;
        font-weight: 800;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }
    .admin-nav-item.active .admin-nav-badge { background: rgba(255,255,255,0.3); }

    /* Footer */
    .admin-sidebar-footer {
        border-top: 1px solid rgba(255,255,255,0.08);
        padding: 14px 16px;
    }
    .admin-sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .admin-sidebar-avatar {
        width: 32px;
        height: 32px;
        background: var(--ikea-yellow);
        color: var(--ikea-dark);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 14px;
        flex-shrink: 0;
    }
    .admin-sidebar-user-name {
        font-size: 13px;
        font-weight: 700;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .admin-sidebar-user-role {
        font-size: 11px;
        color: rgba(255,255,255,0.4);
        text-transform: capitalize;
    }
    .admin-sidebar-logout {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.5);
        border-radius: 6px;
        padding: 7px;
        font-size: 12px;
        font-weight: 700;
        font-family: 'Noto Sans', sans-serif;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .admin-sidebar-logout:hover {
        background: rgba(204,0,8,0.2);
        border-color: rgba(204,0,8,0.4);
        color: #ff6b6b;
    }

    /* ── PUSH CONTENT RIGHT ──────────────────────────────── */
    body.admin-layout .ikea-site-header,
    body.admin-layout main,
    body.admin-layout .ikea-page-subheader {
        margin-left: 240px;
        transition: margin-left 0.25s ease;
    }

    /* Mobile */
    @media (max-width: 900px) {
        .admin-sidebar { transform: translateX(-100%); }
        .admin-sidebar.open { transform: translateX(0); }
        .admin-sidebar-overlay.open { display: block; }
        .admin-sidebar-close { display: block; }

        body.admin-layout .ikea-site-header,
        body.admin-layout main,
        body.admin-layout .ikea-page-subheader {
            margin-left: 0;
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar  = document.getElementById('adminSidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const isOpen   = sidebar.classList.toggle('open');
        overlay.classList.toggle('open', isOpen);
    }
</script>