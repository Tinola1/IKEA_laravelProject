<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Admin Dashboard</h2>
                <p class="admin-page-subtitle">Welcome back — here's what's happening at IKEA Philippines.</p>
            </div>
            <div class="admin-header-actions">
                <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">Manage Products</a>
                <a href="{{ route('admin.orders.index') }}" class="admin-btn-primary">View All Orders</a>
            </div>
        </div>
    </x-slot>

    {{-- ── FLASH MESSAGE ─────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="admin-flash success">{{ session('success') }}</div>
    @endif

    <div class="admin-dashboard">

        {{-- ── KPI STAT CARDS ──────────────────────────────────────── --}}
        <div class="admin-stat-grid">

            {{-- Total Revenue --}}
            <div class="admin-stat-card">
                <div class="admin-stat-icon revenue">₱</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Revenue</div>
                    <div class="admin-stat-value">₱{{ number_format($totalRevenue, 0) }}</div>
                    <div class="admin-stat-meta @if($revenueChange >= 0) positive @else negative @endif">
                        @if($revenueChange >= 0) ↑ @else ↓ @endif
                        {{ abs($revenueChange) }}% vs last month
                        <span>(₱{{ number_format($thisMonthRevenue, 0) }} this month)</span>
                    </div>
                </div>
            </div>

            {{-- Total Orders --}}
            <div class="admin-stat-card">
                <div class="admin-stat-icon orders">📦</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Orders</div>
                    <div class="admin-stat-value">{{ number_format($totalOrders) }}</div>
                    <div class="admin-stat-meta">
                        {{ $thisMonthOrders }} this month
                        @if($lastMonthOrders > 0)
                            <span>({{ $lastMonthOrders }} last month)</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Active Products --}}
            <div class="admin-stat-card">
                <div class="admin-stat-icon products">🛋️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Active Products</div>
                    <div class="admin-stat-value">{{ $totalProducts }}</div>
                    <div class="admin-stat-meta">
                        @if($lowStock->count())
                            <span class="negative">{{ $lowStock->count() }} low/out of stock</span>
                        @else
                            <span class="positive">All products stocked</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Customers --}}
            <div class="admin-stat-card">
                <div class="admin-stat-icon customers">👤</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Customers</div>
                    <div class="admin-stat-value">{{ number_format($totalCustomers) }}</div>
                    <div class="admin-stat-meta">
                        @php
                            $pending   = $statusBreakdown['pending']    ?? 0;
                            $completed = $statusBreakdown['completed']  ?? 0;
                            $cancelled = $statusBreakdown['cancelled']  ?? 0;
                        @endphp
                        <span>{{ $pending }} pending orders</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── ORDER STATUS PILLS ───────────────────────────────────── --}}
        <div class="admin-status-strip">
            @foreach(['pending' => '🕐', 'processing' => '⚙️', 'completed' => '✅', 'cancelled' => '❌'] as $status => $icon)
                <div class="admin-status-pill status-{{ $status }}">
                    <span class="status-icon">{{ $icon }}</span>
                    <span class="status-count">{{ $statusBreakdown[$status] ?? 0 }}</span>
                    <span class="status-label">{{ ucfirst($status) }}</span>
                </div>
            @endforeach
        </div>

        {{-- ── SALES CHART ──────────────────────────────────────────── --}}
        <div class="admin-card chart-card">

            <div class="chart-card-header">
                <div>
                    <h3 class="admin-card-title">Sales Overview</h3>
                    <p class="admin-card-subtitle">Revenue and order volume — {{ $year }}</p>
                </div>
                <div class="chart-controls">

                    {{-- Metric toggle --}}
                    <div class="chart-toggle-group" role="group" aria-label="Chart metric">
                        <button class="chart-toggle active" data-metric="revenue" onclick="setMetric('revenue', this)">Revenue</button>
                        <button class="chart-toggle" data-metric="orders" onclick="setMetric('orders', this)">Orders</button>
                    </div>

                    {{-- Product filter --}}
                    <select id="productFilter" class="admin-select" onchange="filterByProduct(this.value)" aria-label="Filter by product">
                        <option value="all">All Products</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>

                    {{-- Year picker --}}
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="chart-year-form">
                        <select name="year" class="admin-select" onchange="this.form.submit()" aria-label="Select year">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>

                </div>
            </div>

            <div class="chart-wrapper">
                <canvas id="salesChart" aria-label="Sales chart" role="img"></canvas>
            </div>

            {{-- Product revenue breakdown table (shown when a product is selected) --}}
            <div id="productBreakdown" class="product-breakdown" style="display:none;">
                <h4 class="breakdown-title">Monthly Breakdown — <span id="breakdownProductName"></span></h4>
                <div class="breakdown-grid" id="breakdownGrid"></div>
            </div>

        </div>

        {{-- ── BOTTOM GRID: Recent Orders + Low Stock ──────────────── --}}
        <div class="admin-bottom-grid">

            {{-- Recent Orders --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="admin-card-link">View all →</a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="admin-empty">No orders yet.</div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="order-id">#{{ $order->id }}</td>
                                        <td>
                                            <div class="table-customer-name">{{ $order->full_name ?? $order->user?->name ?? '—' }}</div>
                                            <div class="table-customer-email">{{ $order->user?->email }}</div>
                                        </td>
                                        <td class="order-total">₱{{ number_format($order->total, 0) }}</td>
                                        <td>
                                            <span class="order-status-badge status-{{ $order->status }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="order-date">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="table-action-link">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Low Stock Alerts --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        Stock Alerts
                        @if($lowStock->count())
                            <span class="alert-badge">{{ $lowStock->count() }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('admin.inventory.index') }}" class="admin-card-link">Manage stock →</a>
                </div>

                @if($lowStock->isEmpty())
                    <div class="admin-empty">
                        <span style="font-size:32px;">✅</span>
                        <p>All products are well stocked.</p>
                    </div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStock as $product)
                                    <tr>
                                        <td class="table-product-name">{{ $product->name }}</td>
                                        <td class="table-category">{{ $product->category?->name }}</td>
                                        <td>
                                            @if($product->stock === 0)
                                                <span class="stock-badge out">Out of Stock</span>
                                            @else
                                                <span class="stock-badge low">{{ $product->stock }} left</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.inventory.index') }}" class="table-action-link">Restock</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

    </div>{{-- /admin-dashboard --}}

    {{-- ── STYLES ───────────────────────────────────────────────── --}}
    <style>
        /* Layout */
        .admin-dashboard {
            padding: var(--space-lg);
            display: flex;
            flex-direction: column;
            gap: var(--space-lg);
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Page header */
        .admin-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-md);
            flex-wrap: wrap;
        }
        .admin-page-title {
            font-size: var(--text-2xl);
            font-weight: 900;
            color: var(--ikea-dark);
            letter-spacing: -0.5px;
        }
        .admin-page-subtitle {
            font-size: var(--text-sm);
            color: var(--ikea-gray);
            margin-top: 2px;
        }
        .admin-header-actions {
            display: flex;
            gap: var(--space-xs);
        }
        .admin-btn-primary {
            background: var(--ikea-yellow);
            color: var(--ikea-dark);
            font-weight: 700;
            font-size: var(--text-sm);
            padding: 8px 18px;
            border-radius: 40px;
            text-decoration: none;
            transition: background var(--transition-fast);
        }
        .admin-btn-primary:hover { background: #f0cc00; }
        .admin-btn-secondary {
            background: transparent;
            color: var(--ikea-blue);
            font-weight: 700;
            font-size: var(--text-sm);
            padding: 8px 18px;
            border-radius: 40px;
            border: 2px solid var(--ikea-blue);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        .admin-btn-secondary:hover { background: var(--ikea-blue); color: white; }

        /* Flash */
        .admin-flash {
            padding: 12px var(--space-md);
            border-radius: 6px;
            font-size: var(--text-sm);
            font-weight: 600;
            margin: 0 var(--space-lg);
        }
        .admin-flash.success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4caf50; }

        /* Stat cards */
        .admin-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--space-md);
        }
        .admin-stat-card {
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 8px;
            padding: var(--space-md);
            display: flex;
            gap: var(--space-sm);
            align-items: flex-start;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition-fast), transform var(--transition-fast);
        }
        .admin-stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .admin-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .admin-stat-icon.revenue   { background: #fff8e1; }
        .admin-stat-icon.orders    { background: #e3f2fd; }
        .admin-stat-icon.products  { background: #f3e5f5; }
        .admin-stat-icon.customers { background: #e8f5e9; }
        .admin-stat-label { font-size: var(--text-xs); font-weight: 700; color: var(--ikea-gray); text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-stat-value { font-size: 28px; font-weight: 900; color: var(--ikea-dark); letter-spacing: -1px; line-height: 1.1; margin: 4px 0 6px; }
        .admin-stat-meta  { font-size: var(--text-xs); color: var(--ikea-gray); }
        .admin-stat-meta.positive { color: #2e7d32; }
        .admin-stat-meta.negative { color: #CC0008; }
        .admin-stat-meta span { opacity: 0.7; margin-left: 4px; }

        /* Status strip */
        .admin-status-strip {
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
        }
        .admin-status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 40px;
            padding: 8px 16px;
            font-size: var(--text-sm);
            box-shadow: var(--shadow-sm);
        }
        .status-icon { font-size: 16px; }
        .status-count { font-weight: 900; font-size: 18px; color: var(--ikea-dark); }
        .status-label { color: var(--ikea-gray); font-weight: 600; }
        .status-pending    .status-count { color: #f57c00; }
        .status-processing .status-count { color: var(--ikea-blue); }
        .status-completed  .status-count { color: #2e7d32; }
        .status-cancelled  .status-count { color: #CC0008; }

        /* Cards */
        .admin-card {
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .admin-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-md) var(--space-md) 0;
        }
        .admin-card-title {
            font-size: var(--text-lg);
            font-weight: 800;
            color: var(--ikea-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-card-subtitle {
            font-size: var(--text-sm);
            color: var(--ikea-gray);
            margin-top: 2px;
        }
        .admin-card-link {
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--ikea-blue);
            text-decoration: none;
        }
        .admin-card-link:hover { text-decoration: underline; }
        .admin-empty {
            padding: var(--space-xl);
            text-align: center;
            color: var(--ikea-gray);
            font-size: var(--text-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* Chart card */
        .chart-card { padding-bottom: 0; }
        .chart-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: var(--space-sm);
            padding: var(--space-md);
        }
        .chart-controls {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            flex-wrap: wrap;
        }
        .chart-toggle-group {
            display: flex;
            background: var(--ikea-light);
            border-radius: 6px;
            padding: 3px;
            gap: 2px;
        }
        .chart-toggle {
            padding: 5px 14px;
            border-radius: 4px;
            border: none;
            background: transparent;
            font-size: var(--text-sm);
            font-weight: 600;
            font-family: 'Noto Sans', sans-serif;
            color: var(--ikea-gray);
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .chart-toggle.active { background: white; color: var(--ikea-dark); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .admin-select {
            height: 36px;
            padding: 0 10px;
            border: 1px solid var(--ikea-border);
            border-radius: 6px;
            font-size: var(--text-sm);
            font-family: 'Noto Sans', sans-serif;
            font-weight: 600;
            color: var(--ikea-dark);
            background: white;
            cursor: pointer;
        }
        .admin-select:focus { outline: none; border-color: var(--ikea-blue); }
        .chart-year-form { display: inline; }
        .chart-wrapper {
            padding: var(--space-sm) var(--space-md) var(--space-md);
            height: 320px;
            position: relative;
        }

        /* Product breakdown */
        .product-breakdown {
            border-top: 1px solid var(--ikea-border);
            padding: var(--space-md);
        }
        .breakdown-title {
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--ikea-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--space-sm);
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
        }
        .breakdown-month {
            background: var(--ikea-light);
            border-radius: 6px;
            padding: 10px 8px;
            text-align: center;
        }
        .breakdown-month-label { font-size: 11px; color: var(--ikea-gray); font-weight: 700; }
        .breakdown-month-value { font-size: var(--text-sm); font-weight: 800; color: var(--ikea-dark); margin-top: 2px; }

        /* Bottom grid */
        .admin-bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-md);
        }

        /* Tables */
        .admin-table-wrap { overflow-x: auto; }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: var(--text-sm);
        }
        .admin-table th {
            background: var(--ikea-light);
            color: var(--ikea-gray);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            text-align: left;
            border-bottom: 1px solid var(--ikea-border);
        }
        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--ikea-border);
            color: var(--ikea-dark);
        }
        .admin-table tbody tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: #fafafa; }
        .order-id { font-weight: 700; color: var(--ikea-blue); font-size: 12px; }
        .table-customer-name { font-weight: 600; }
        .table-customer-email { font-size: 11px; color: var(--ikea-gray); }
        .order-total { font-weight: 700; }
        .order-date { color: var(--ikea-gray); font-size: 12px; white-space: nowrap; }
        .table-product-name { font-weight: 600; max-width: 160px; }
        .table-category { font-size: 12px; color: var(--ikea-gray); }
        .table-action-link { font-size: 12px; font-weight: 700; color: var(--ikea-blue); text-decoration: none; }
        .table-action-link:hover { text-decoration: underline; }

        /* Order status badges */
        .order-status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .order-status-badge.status-pending    { background: #fff3e0; color: #f57c00; }
        .order-status-badge.status-processing { background: #e3f2fd; color: #1565c0; }
        .order-status-badge.status-completed  { background: #e8f5e9; color: #2e7d32; }
        .order-status-badge.status-cancelled  { background: #ffebee; color: #CC0008; }

        /* Stock badges */
        .alert-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: #CC0008;
            color: white;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
        }
        .stock-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 700;
        }
        .stock-badge.out { background: #ffebee; color: #CC0008; }
        .stock-badge.low { background: #fff3e0; color: #f57c00; }

        /* Responsive */
        @media (max-width: 1100px) {
            .admin-stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 900px) {
            .admin-dashboard { padding: var(--space-md); }
            .admin-bottom-grid { grid-template-columns: 1fr; }
            .breakdown-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 600px) {
            .admin-stat-grid { grid-template-columns: 1fr; }
            .admin-page-header { flex-direction: column; align-items: flex-start; }
            .chart-card-header { flex-direction: column; }
            .breakdown-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>

    {{-- ── CHART JS ─────────────────────────────────────────────── --}}
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Data from PHP ────────────────────────────────────────
        const labels       = @json($chartLabels);
        const allRevenue   = @json($chartRevenue);
        const allOrders    = @json($chartOrders);
        const productData  = @json($productChartJson);  // [{id, name, data:[12 values]}]

        // ── State ────────────────────────────────────────────────
        let currentMetric  = 'revenue';
        let currentProduct = 'all';

        // ── Chart init ───────────────────────────────────────────
        const ctx = document.getElementById('salesChart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: allRevenue,
                    backgroundColor: 'rgba(0, 88, 163, 0.15)',
                    borderColor:     '#0058A3',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    type: 'bar',
                }, {
                    label: 'Trend',
                    data: allRevenue,
                    type: 'line',
                    borderColor: '#FFDB00',
                    backgroundColor: 'rgba(255, 219, 0, 0.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#FFDB00',
                    pointBorderColor: '#0058A3',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleFont: { family: 'Noto Sans', weight: 'bold' },
                        bodyFont:  { family: 'Noto Sans' },
                        padding: 12,
                        callbacks: {
                            label: function(ctx) {
                                if (currentMetric === 'revenue') {
                                    return '  ₱' + ctx.parsed.y.toLocaleString('en-PH');
                                }
                                return '  ' + ctx.parsed.y + ' orders';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Noto Sans', weight: '600' }, color: '#767676' }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { family: 'Noto Sans' },
                            color: '#767676',
                            callback: function(v) {
                                return currentMetric === 'revenue'
                                    ? '₱' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                                    : v;
                            }
                        }
                    }
                }
            }
        });

        // ── Helpers ──────────────────────────────────────────────
        function getActiveData() {
            if (currentProduct === 'all') {
                return currentMetric === 'revenue' ? allRevenue : allOrders;
            }
            const p = productData.find(p => String(p.id) === String(currentProduct));
            return p ? p.data : Array(12).fill(0);
        }

        function getLabel() {
            if (currentProduct !== 'all') {
                const p = productData.find(p => String(p.id) === String(currentProduct));
                return p ? p.name + ' — Revenue (₱)' : 'Revenue (₱)';
            }
            return currentMetric === 'revenue' ? 'Revenue (₱)' : 'Orders';
        }

        function updateChart() {
            const data = getActiveData();
            chart.data.datasets[0].data = data;
            chart.data.datasets[1].data = data;
            chart.data.datasets[0].label = getLabel();
            chart.update('active');
        }

        function updateBreakdown(productId) {
            const panel = document.getElementById('productBreakdown');
            const grid  = document.getElementById('breakdownGrid');
            const nameEl = document.getElementById('breakdownProductName');

            if (productId === 'all') {
                panel.style.display = 'none';
                return;
            }

            const p = productData.find(p => String(p.id) === String(productId));
            if (!p) { panel.style.display = 'none'; return; }

            nameEl.textContent = p.name;
            grid.innerHTML = labels.map((m, i) => `
                <div class="breakdown-month">
                    <div class="breakdown-month-label">${m}</div>
                    <div class="breakdown-month-value">
                        ${p.data[i] > 0 ? '₱' + p.data[i].toLocaleString('en-PH', {maximumFractionDigits:0}) : '—'}
                    </div>
                </div>
            `).join('');

            panel.style.display = 'block';
        }

        // ── Public handlers (called by inline onclick) ───────────
        window.setMetric = function(metric, btn) {
            currentMetric = metric;
            document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Hide orders toggle when a specific product is shown (no per-product orders data)
            if (currentProduct !== 'all' && metric === 'orders') return;
            updateChart();
        };

        window.filterByProduct = function(productId) {
            currentProduct = productId;

            // When a specific product is selected, always show revenue
            if (productId !== 'all') {
                document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
                document.querySelector('[data-metric="revenue"]').classList.add('active');
                currentMetric = 'revenue';
            }

            updateChart();
            updateBreakdown(productId);
        };

    });
    </script>
    @endpush

</x-app-layout>