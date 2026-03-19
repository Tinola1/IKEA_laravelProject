<x-admin-layout>
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

    <div class="admin-dashboard">

        {{-- ── STATUS STRIP ────────────────────────────────────── --}}
        @php
            $statusCounts = \App\Models\Order::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status');
        @endphp
        <div class="admin-status-strip">
            @foreach(['pending' => '🕐', 'processing' => '⚙️', 'completed' => '✅', 'cancelled' => '❌'] as $status => $icon)
                <div class="admin-status-pill status-{{ $status }}">
                    <span class="status-icon">{{ $icon }}</span>
                    <span class="status-count">{{ $statusCounts[$status] ?? 0 }}</span>
                    <span class="status-label">{{ ucfirst($status) }}</span>
                </div>
            @endforeach
        </div>

        {{-- ── FILTER BAR ───────────────────────────────────────── --}}
        <div class="admin-card" style="padding: 14px var(--space-md);">
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

        {{-- ── TABLE ───────────────────────────────────────────── --}}
        <div class="admin-card" style="padding:var(--space-md);">

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
                            <td class="order-date">{{ $order->created_at->format('M d, Y') }}</td>
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
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="table-action-link">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:48px;text-align:center;color:var(--ikea-gray);">
                                No orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <style>
        .orders-filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .filter-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--ikea-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .admin-date-input {
            height: 36px;
            padding: 0 10px;
            border: 1px solid var(--ikea-border);
            border-radius: 6px;
            font-size: var(--text-sm);
            font-family: 'Noto Sans', sans-serif;
            color: var(--ikea-dark);
            background: white;
            cursor: pointer;
        }
        .admin-date-input:focus { outline: none; border-color: var(--ikea-blue); }
        .btn-clear-filters {
            height: 36px;
            padding: 0 16px;
            background: transparent;
            border: 1px solid var(--ikea-border);
            border-radius: 6px;
            font-size: var(--text-sm);
            font-weight: 700;
            font-family: 'Noto Sans', sans-serif;
            color: var(--ikea-gray);
            cursor: pointer;
            transition: all .15s;
        }
        .btn-clear-filters:hover {
            background: var(--ikea-light);
            border-color: var(--ikea-gray);
        }
    </style>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            var table;

            $(document).ready(function () {
                table = $('#ordersTable').DataTable({
                    pageLength: 15,
                    order: [[0, 'desc']],
                    columnDefs: [{ orderable: false, targets: [6] }],
                    language: {
                        search: 'Search orders:',
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

                var rowStatus  = data[5].trim().toLowerCase();
                var rowPayment = data[4].trim().toLowerCase();
                var rowDate    = data[2];

                if (statusFilter  && rowStatus  !== statusFilter)  return false;
                if (paymentFilter && rowPayment !== paymentFilter)  return false;

                if (dateFrom || dateTo) {
                    var d = new Date(rowDate);
                    if (dateFrom && d < new Date(dateFrom)) return false;
                    if (dateTo   && d > new Date(dateTo))   return false;
                }

                return true;
            });

            function filterOrders() {
                if (table) table.draw();
            }

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