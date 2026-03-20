<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>
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

    @if(session('success'))
        <div class="admin-flash success">{{ session('success') }}</div>
    @endif

    <div class="admin-dashboard">

        {{-- ── KPI STAT CARDS ──────────────────────────────────────── --}}
        <div class="admin-stat-grid">
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
            <div class="admin-stat-card">
                <div class="admin-stat-icon orders">📦</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Orders</div>
                    <div class="admin-stat-value">{{ number_format($totalOrders) }}</div>
                    <div class="admin-stat-meta">
                        {{ $thisMonthOrders }} this month
                        @if($lastMonthOrders > 0)<span>({{ $lastMonthOrders }} last month)</span>@endif
                    </div>
                </div>
            </div>
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
            <div class="admin-stat-card">
                <div class="admin-stat-icon customers">👤</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Customers</div>
                    <div class="admin-stat-value">{{ number_format($totalCustomers) }}</div>
                    <div class="admin-stat-meta">
                        @php $pending = $statusBreakdown['pending'] ?? 0; @endphp
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

        {{-- ══════════════════════════════════════════════════════════════
             CHART 1 — YEARLY SALES (bar + trend line, year picker)
        ══════════════════════════════════════════════════════════════ --}}
        <div class="admin-card chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="admin-card-title">Yearly Sales Overview</h3>
                    <p class="admin-card-subtitle">Monthly revenue and order volume — {{ $year }}</p>
                </div>
                <div class="chart-controls">
                    <div class="chart-toggle-group">
                        <button class="chart-toggle active" data-metric="revenue" onclick="setMetric('revenue', this)">Revenue</button>
                        <button class="chart-toggle" data-metric="orders" onclick="setMetric('orders', this)">Orders</button>
                    </div>
                    <select id="productFilter" class="admin-select" onchange="filterByProduct(this.value)">
                        <option value="all">All Products</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="chart-year-form" id="yearForm">
                        {{-- Preserve date range when changing year --}}
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
                        <select name="year" class="admin-select" onchange="this.form.submit()">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="salesChart"></canvas>
            </div>
            <div id="productBreakdown" class="product-breakdown" style="display:none;">
                <h4 class="breakdown-title">Monthly Breakdown — <span id="breakdownProductName"></span></h4>
                <div class="breakdown-grid" id="breakdownGrid"></div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             CHART 2 — DATE RANGE BAR CHART (with date pickers)
        ══════════════════════════════════════════════════════════════ --}}
        <div class="admin-card chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="admin-card-title">Sales by Date Range</h3>
                    <p class="admin-card-subtitle">
                        {{ $dateFrom->format('M d, Y') }} — {{ $dateTo->format('M d, Y') }}
                        &nbsp;·&nbsp;
                        ₱{{ number_format(array_sum($rangeRevenue), 0) }} total revenue
                    </p>
                </div>
                <div class="chart-controls">
                    {{-- Revenue / Orders toggle --}}
                    <div class="chart-toggle-group">
                        <button class="chart-toggle active" onclick="setRangeMetric('revenue', this)">Revenue</button>
                        <button class="chart-toggle" onclick="setRangeMetric('orders', this)">Orders</button>
                    </div>
                    {{-- Date picker form --}}
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="range-form" id="rangeForm">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <div class="range-inputs">
                            <div class="range-input-group">
                                <label class="range-label">From</label>
                                <input type="date"
                                       name="date_from"
                                       class="admin-select"
                                       value="{{ $dateFrom->format('Y-m-d') }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       onchange="document.getElementById('rangeForm').submit()">
                            </div>
                            <span class="range-sep">→</span>
                            <div class="range-input-group">
                                <label class="range-label">To</label>
                                <input type="date"
                                       name="date_to"
                                       class="admin-select"
                                       value="{{ $dateTo->format('Y-m-d') }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       onchange="document.getElementById('rangeForm').submit()">
                            </div>
                        </div>
                    </form>
                    {{-- Quick range presets --}}
                    <div class="range-presets">
                        <a href="{{ route('admin.dashboard', array_merge(request()->only('year'), ['date_from' => now()->subDays(6)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')])) }}"
                           class="range-preset {{ request('date_from') == now()->subDays(6)->format('Y-m-d') ? 'active' : '' }}">7d</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->only('year'), ['date_from' => now()->subDays(29)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')])) }}"
                           class="range-preset {{ !request('date_from') || request('date_from') == now()->subDays(29)->format('Y-m-d') ? 'active' : '' }}">30d</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->only('year'), ['date_from' => now()->subDays(89)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')])) }}"
                           class="range-preset {{ request('date_from') == now()->subDays(89)->format('Y-m-d') ? 'active' : '' }}">90d</a>
                        <a href="{{ route('admin.dashboard', array_merge(request()->only('year'), ['date_from' => now()->startOfYear()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')])) }}"
                           class="range-preset {{ request('date_from') == now()->startOfYear()->format('Y-m-d') ? 'active' : '' }}">YTD</a>
                    </div>
                </div>
            </div>

            @if(empty($rangeLabels))
                <div class="admin-empty" style="height:280px;justify-content:center;">
                    <span style="font-size:36px;">📭</span>
                    <p>No orders in this date range.</p>
                </div>
            @else
                <div class="chart-wrapper">
                    <canvas id="rangeChart"></canvas>
                </div>

                {{-- Summary strip below chart --}}
                <div class="range-summary">
                    <div class="range-summary-item">
                        <span class="range-summary-label">Total Revenue</span>
                        <span class="range-summary-value">₱{{ number_format(array_sum($rangeRevenue), 0) }}</span>
                    </div>
                    <div class="range-summary-item">
                        <span class="range-summary-label">Total Orders</span>
                        <span class="range-summary-value">{{ array_sum($rangeOrders) }}</span>
                    </div>
                    <div class="range-summary-item">
                        <span class="range-summary-label">Avg. per Period</span>
                        <span class="range-summary-value">
                            ₱{{ count($rangeRevenue) ? number_format(array_sum($rangeRevenue) / count($rangeRevenue), 0) : 0 }}
                        </span>
                    </div>
                    <div class="range-summary-item">
                        <span class="range-summary-label">Peak Period</span>
                        <span class="range-summary-value">
                            @php
                                $peakIdx = array_search(max($rangeRevenue), $rangeRevenue);
                            @endphp
                            {{ $rangeLabels[$peakIdx] ?? '—' }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             CHART 3 — PIE CHART (revenue % per product)
        ══════════════════════════════════════════════════════════════ --}}
        <div class="admin-card chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="admin-card-title">Sales Share by Product</h3>
                    <p class="admin-card-subtitle">Revenue percentage breakdown across all products (excluding cancelled orders)</p>
                </div>
                <div class="chart-controls">
                    <div class="chart-toggle-group">
                        <button class="chart-toggle active" onclick="setPieType('doughnut', this)">Doughnut</button>
                        <button class="chart-toggle" onclick="setPieType('pie', this)">Pie</button>
                    </div>
                </div>
            </div>

            @if(empty($pieLabels))
                <div class="admin-empty" style="height:320px;justify-content:center;">
                    <span style="font-size:36px;">📭</span>
                    <p>No sales data yet.</p>
                </div>
            @else
                <div class="pie-layout">
                    {{-- Chart --}}
                    <div class="pie-chart-wrap">
                        <canvas id="pieChart"></canvas>
                    </div>

                    {{-- Legend table --}}
                    <div class="pie-legend">
                        <div class="pie-legend-header">
                            <span>Product</span>
                            <span>Revenue</span>
                            <span>Share</span>
                        </div>
                        @foreach($pieLabels as $i => $label)
                            <div class="pie-legend-row" data-index="{{ $i }}">
                                <div class="pie-legend-name">
                                    <span class="pie-legend-dot" style="background:var(--pie-color-{{ $i }})"></span>
                                    {{ $label }}
                                </div>
                                <span class="pie-legend-revenue">₱{{ number_format($pieRevenues[$i], 0) }}</span>
                                <span class="pie-legend-pct">{{ $piePercentages[$i] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ── BOTTOM GRID: Recent Orders + Low Stock ──────────────── --}}
        <div class="admin-bottom-grid">
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
                                    <th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th>
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
                                            <span class="order-status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td class="order-date">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td><a href="{{ route('admin.orders.show', $order) }}" class="table-action-link">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        Stock Alerts
                        @if($lowStock->count())<span class="alert-badge">{{ $lowStock->count() }}</span>@endif
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
                            <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th></th></tr></thead>
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
                                        <td><a href="{{ route('admin.inventory.index') }}" class="table-action-link">Restock</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Shared colour palette ────────────────────────────────
        const PIE_COLORS = [
            '#0058A3','#FFDB00','#2e7d32','#f57c00',
            '#9c27b0','#CC0008','#00838f','#5d4037','#546e7a'
        ];

        // ════════════════════════════════════════════════════════
        // CHART 1 — Yearly bar + trend line
        // ════════════════════════════════════════════════════════
        const labels      = @json($chartLabels);
        const allRevenue  = @json($chartRevenue);
        const allOrders   = @json($chartOrders);
        const productData = @json($productChartJson);

        let currentMetric  = 'revenue';
        let currentProduct = 'all';

        const ctx1  = document.getElementById('salesChart').getContext('2d');
        const chart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: allRevenue,
                    backgroundColor: 'rgba(0,88,163,0.15)',
                    borderColor: '#0058A3',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    type: 'bar',
                }, {
                    label: 'Trend',
                    data: allRevenue,
                    type: 'line',
                    borderColor: '#FFDB00',
                    backgroundColor: 'rgba(255,219,0,0.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#FFDB00',
                    pointBorderColor: '#0058A3',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleFont: { family: 'Noto Sans', weight: 'bold' },
                        bodyFont:  { family: 'Noto Sans' },
                        padding: 12,
                        callbacks: {
                            label: ctx => currentMetric === 'revenue'
                                ? '  ₱' + ctx.parsed.y.toLocaleString('en-PH')
                                : '  ' + ctx.parsed.y + ' orders'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Noto Sans', weight: '600' }, color: '#767676' } },
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { family: 'Noto Sans' }, color: '#767676',
                        callback: v => currentMetric === 'revenue'
                            ? '₱' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) : v
                    }}
                }
            }
        });

        function getActiveData() {
            if (currentProduct === 'all') return currentMetric === 'revenue' ? allRevenue : allOrders;
            const p = productData.find(p => String(p.id) === String(currentProduct));
            return p ? p.data : Array(12).fill(0);
        }
        function updateChart1() {
            const data = getActiveData();
            chart.data.datasets[0].data = data;
            chart.data.datasets[1].data = data;
            chart.update('active');
        }
        function updateBreakdown(productId) {
            const panel = document.getElementById('productBreakdown');
            if (productId === 'all') { panel.style.display = 'none'; return; }
            const p = productData.find(p => String(p.id) === String(productId));
            if (!p) { panel.style.display = 'none'; return; }
            document.getElementById('breakdownProductName').textContent = p.name;
            document.getElementById('breakdownGrid').innerHTML = labels.map((m, i) => `
                <div class="breakdown-month">
                    <div class="breakdown-month-label">${m}</div>
                    <div class="breakdown-month-value">${p.data[i] > 0 ? '₱' + p.data[i].toLocaleString('en-PH', {maximumFractionDigits:0}) : '—'}</div>
                </div>`).join('');
            panel.style.display = 'block';
        }

        window.setMetric = function(metric, btn) {
            currentMetric = metric;
            document.querySelectorAll('[data-metric]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (currentProduct !== 'all' && metric === 'orders') return;
            updateChart1();
        };
        window.filterByProduct = function(productId) {
            currentProduct = productId;
            if (productId !== 'all') {
                document.querySelectorAll('[data-metric]').forEach(b => b.classList.remove('active'));
                document.querySelector('[data-metric="revenue"]').classList.add('active');
                currentMetric = 'revenue';
            }
            updateChart1();
            updateBreakdown(productId);
        };

        // ════════════════════════════════════════════════════════
        // CHART 2 — Date range bar chart
        // ════════════════════════════════════════════════════════
        const rangeCanvas = document.getElementById('rangeChart');
        if (rangeCanvas) {
            const rangeLabels  = @json($rangeLabels);
            const rangeRevenue = @json($rangeRevenue);
            const rangeOrders  = @json($rangeOrders);
            let rangeMetric    = 'revenue';

            const ctx2      = rangeCanvas.getContext('2d');
            const rangeChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: rangeLabels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: rangeRevenue,
                        backgroundColor: ctx => {
                            const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            g.addColorStop(0, 'rgba(0,88,163,0.8)');
                            g.addColorStop(1, 'rgba(0,88,163,0.2)');
                            return g;
                        },
                        borderColor: '#0058A3',
                        borderWidth: 0,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1a1a',
                            titleFont: { family: 'Noto Sans', weight: 'bold' },
                            bodyFont:  { family: 'Noto Sans' },
                            padding: 12,
                            callbacks: {
                                label: ctx => rangeMetric === 'revenue'
                                    ? '  ₱' + ctx.parsed.y.toLocaleString('en-PH')
                                    : '  ' + ctx.parsed.y + ' orders'
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Noto Sans', weight: '600' }, color: '#767676', maxRotation: 45 } },
                        y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { family: 'Noto Sans' }, color: '#767676',
                            callback: v => rangeMetric === 'revenue'
                                ? '₱' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) : v
                        }}
                    }
                }
            });

            window.setRangeMetric = function(metric, btn) {
                rangeMetric = metric;
                btn.closest('.chart-toggle-group').querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                rangeChart.data.datasets[0].data  = metric === 'revenue' ? rangeRevenue : rangeOrders;
                rangeChart.data.datasets[0].label = metric === 'revenue' ? 'Revenue (₱)' : 'Orders';
                rangeChart.update('active');
            };
        } else {
            window.setRangeMetric = function() {};
        }

        // ════════════════════════════════════════════════════════
        // CHART 3 — Pie / Doughnut
        // ════════════════════════════════════════════════════════
        const pieCanvas = document.getElementById('pieChart');
        if (pieCanvas) {
            const pieLabels      = @json($pieLabels);
            const pieRevenues    = @json($pieRevenues);
            const piePercentages = @json($piePercentages);

            const ctx3   = pieCanvas.getContext('2d');
            let pieChart = new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieRevenues,
                        backgroundColor: PIE_COLORS.slice(0, pieLabels.length),
                        borderColor: 'white',
                        borderWidth: 3,
                        hoverOffset: 12,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1a1a',
                            titleFont: { family: 'Noto Sans', weight: 'bold' },
                            bodyFont:  { family: 'Noto Sans' },
                            padding: 12,
                            callbacks: {
                                label: ctx => {
                                    const pct = piePercentages[ctx.dataIndex];
                                    const val = ctx.parsed.toLocaleString('en-PH', { maximumFractionDigits: 0 });
                                    return `  ₱${val}  (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Highlight legend row on hover
            pieCanvas.addEventListener('mousemove', evt => {
                const points = pieChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                document.querySelectorAll('.pie-legend-row').forEach(r => r.style.background = '');
                if (points.length) {
                    const idx = points[0].index;
                    const row = document.querySelector(`.pie-legend-row[data-index="${idx}"]`);
                    if (row) row.style.background = 'var(--ikea-light)';
                }
            });

            window.setPieType = function(type, btn) {
                btn.closest('.chart-toggle-group').querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                pieChart.config.type = type;
                pieChart.options.cutout = type === 'doughnut' ? '60%' : '0%';
                pieChart.update();
            };
        } else {
            window.setPieType = function() {};
        }

    });
    </script>
    @endpush

</x-admin-layout>