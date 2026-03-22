<x-admin-layout>
    <x-slot name="title">Orders</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Order Management</h2>
                <p class="admin-page-subtitle">{{ $orders->count() }} total orders</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">

        {{-- STAT CARDS --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(5,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon revenue">₱</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Revenue</div>
                    <div class="admin-stat-value">₱{{ number_format($totalRevenue, 0) }}</div>
                    <div class="admin-stat-meta">excluding cancelled</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fff3e0;">🕐</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Pending</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $statusCounts['pending'] ?? 0 }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e3f2fd;">⚙️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Processing</div>
                    <div class="admin-stat-value" style="color:#1565c0;">{{ $statusCounts['processing'] ?? 0 }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e8f5e9;">✅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Completed</div>
                    <div class="admin-stat-value" style="color:#2e7d32;">{{ $statusCounts['completed'] ?? 0 }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#ffebee;">❌</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Cancelled</div>
                    <div class="admin-stat-value" style="color:#CC0008;">{{ $statusCounts['cancelled'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="admin-card" style="padding:14px var(--space-md);">
            <div class="orders-filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select id="statusFilter" class="admin-select" onchange="filterOrders()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Payment</label>
                    <select id="paymentFilter" class="admin-select" onchange="filterOrders()">
                        <option value="">All</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date From</label>
                    <input type="date" id="dateFrom" class="admin-date-input" onchange="filterOrders()">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date To</label>
                    <input type="date" id="dateTo" class="admin-date-input" onchange="filterOrders()">
                </div>
                <div class="filter-group" style="align-self:flex-end;">
                    <button onclick="clearFilters()" class="btn-clear-filters">Clear</button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="admin-card" style="padding:var(--space-md);">

            {{-- TOOLBAR --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <button onclick="location.reload()" class="btn-clear-filters">↻ Refresh</button>
            </div>

            <table id="ordersTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="order-id">#{{ $order->id }}</td>
                            <td>
                                <div class="table-customer-name">{{ $order->user->name }}</div>
                                <div class="table-customer-email">{{ $order->user->email }}</div>
                            </td>
                            <td class="order-date">{{ $order->created_at->format('M d, Y g:i A') }}</td>
                            <td class="order-total">₱{{ number_format($order->total, 0) }}</td>
                            <td>
                                <span class="order-status-badge {{ $order->payment_status === 'paid' ? 'status-completed' : 'status-cancelled' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="order-status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="table-action-link">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="admin-empty-row">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            var table;
            $(document).ready(function () {
                table = $('#ordersTable').DataTable({
                    pageLength: 15,
                    stateSave: true,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    order: [[0, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [6] },
                        { type: 'num', targets: [0], render: d => d.replace('#','') },
                    ],
                    language: {
                        search: 'Search orders:',
                        lengthMenu: 'Show _MENU_ orders',
                        info: 'Showing _START_–_END_ of _TOTAL_ orders',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== 'ordersTable') return true;
                var statusFilter  = document.getElementById('statusFilter').value.toLowerCase();
                var paymentFilter = document.getElementById('paymentFilter').value.toLowerCase();
                var dateFrom      = document.getElementById('dateFrom').value;
                var dateTo        = document.getElementById('dateTo').value;
                var rowStatus     = data[5].trim().toLowerCase();
                var rowPayment    = data[4].trim().toLowerCase();
                var rowDate       = data[2];
                if (statusFilter  && rowStatus  !== statusFilter)  return false;
                if (paymentFilter && rowPayment !== paymentFilter)  return false;
                if (dateFrom || dateTo) {
                    var d = new Date(rowDate);
                    if (dateFrom && d < new Date(dateFrom)) return false;
                    if (dateTo   && d > new Date(dateTo))   return false;
                }
                return true;
            });

            function filterOrders() { if (table) table.draw(); }
            function clearFilters() {
                document.getElementById('statusFilter').value  = '';
                document.getElementById('paymentFilter').value = '';
                document.getElementById('dateFrom').value      = '';
                document.getElementById('dateTo').value        = '';
                if (table) table.draw();
            }
        </script>
    @endpush

</x-admin-layout>