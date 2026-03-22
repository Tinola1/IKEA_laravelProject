<x-admin-layout>
    <x-slot name="title">Appointments</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Appointments</h2>
                <p class="admin-page-subtitle">Manage showroom planning sessions.</p>
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
        <div class="admin-stat-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fff3e0;">🕐</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Pending</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $pendingCount }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e3f2fd;">✅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Confirmed</div>
                    <div class="admin-stat-value" style="color:#1565c0;">{{ $confirmedCount }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e8f5e9;">📅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Today</div>
                    <div class="admin-stat-value" style="color:#2e7d32;">{{ $todayCount }}</div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="admin-card" style="padding:14px var(--space-md);">
            <div class="orders-filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select id="statusFilter" class="admin-select" onchange="filterTable()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Service</label>
                    <select id="serviceFilter" class="admin-select" onchange="filterTable()">
                        <option value="">All Services</option>
                        @foreach(\App\Models\Appointment::serviceTypes() as $key => $label)
                            <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group" style="align-self:flex-end;">
                    <button onclick="clearFilters()" class="btn-clear-filters">Clear</button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="admin-card" style="padding:var(--space-md);">
            <table id="appointmentsTable" class="admin-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Staff</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr>
                            <td class="order-id">#{{ str_pad($appt->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="table-customer-name">{{ $appt->full_name }}</div>
                                <div class="table-customer-email">{{ $appt->user->email }}</div>
                            </td>
                            <td class="table-product-name">{{ $appt->serviceLabel() }}</td>
                            <td>
                                <div style="font-weight:700;">{{ $appt->appointment_date->format('M d, Y') }}</div>
                                <div style="font-size:12px;color:var(--ikea-gray);">
                                    {{ \App\Models\Appointment::timeSlots()[$appt->appointment_time] ?? $appt->appointment_time }}
                                </div>
                            </td>
                            <td style="font-size:13px;">
                                {{ $appt->staff?->name ?? '—' }}
                            </td>
                            <td>
                                <span class="order-status-badge {{ $appt->statusColor() }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <a href="{{ route('admin.appointments.show', $appt) }}" class="btn-edit">Manage</a>
                                    <form method="POST" action="{{ route('admin.appointments.destroy', $appt) }}"
                                          onsubmit="return confirm('Delete this appointment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="admin-empty-row">No appointments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($appointments->hasPages())
                <div style="padding:var(--space-md) 0 0;">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            var table;
            $(document).ready(function () {
                table = $('#appointmentsTable').DataTable({
                    pageLength: 20,
                    stateSave: true,
                    lengthMenu: [[20, 50, -1], [20, 50, 'All']],
                    order: [[3, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [6] },
                        { type: 'num', targets: [0], render: d => d.replace('#', '') },
                    ],
                    language: {
                        search: 'Search appointments:',
                        info: 'Showing _START_–_END_ of _TOTAL_ appointments',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== 'appointmentsTable') return true;
                var statusFilter  = document.getElementById('statusFilter').value.toLowerCase();
                var serviceFilter = document.getElementById('serviceFilter').value.toLowerCase();
                var rowStatus     = data[5].trim().toLowerCase();
                var rowService    = data[2].trim().toLowerCase();
                if (statusFilter  && rowStatus  !== statusFilter)                  return false;
                if (serviceFilter && !rowService.includes(serviceFilter.slice(2))) return false;
                return true;
            });

            function filterTable()  { if (table) table.draw(); }
            function clearFilters() {
                document.getElementById('statusFilter').value  = '';
                document.getElementById('serviceFilter').value = '';
                if (table) table.draw();
            }
        </script>
    @endpush

</x-admin-layout>