<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Showroom Appointments</h2>
                <p class="admin-page-subtitle">Manage customer planning sessions.</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-dashboard">

        {{-- Stat strip --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon orders">🕐</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Pending</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $pendingCount }}</div>
                    <div class="admin-stat-meta">Awaiting confirmation</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon products">✅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Confirmed</div>
                    <div class="admin-stat-value" style="color:var(--ikea-blue);">{{ $confirmedCount }}</div>
                    <div class="admin-stat-meta">Upcoming sessions</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon customers">📅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Today</div>
                    <div class="admin-stat-value">{{ $todayCount }}</div>
                    <div class="admin-stat-meta">Appointments today</div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="admin-card" style="padding:var(--space-md);">
            <table id="appointmentsTable" class="admin-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Staff</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr>
                            <td class="order-id">#{{ str_pad($appt->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="table-customer-name">{{ $appt->full_name }}</div>
                                <div class="table-customer-email">{{ $appt->email }}</div>
                            </td>
                            <td style="font-size:13px;font-weight:600;">{{ $appt->serviceLabel() }}</td>
                            <td>
                                <div style="font-weight:700;font-size:13px;">
                                    {{ $appt->appointment_date->format('M d, Y') }}
                                </div>
                                <div style="font-size:12px;color:var(--ikea-gray);">
                                    {{ \App\Models\Appointment::timeSlots()[$appt->appointment_time] ?? $appt->appointment_time }}
                                </div>
                            </td>
                            <td>
                                <span class="order-status-badge {{ $appt->statusColor() }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td style="font-size:13px;color:var(--ikea-gray);">
                                {{ $appt->staff?->name ?? '—' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.appointments.show', $appt) }}"
                                   class="table-action-link">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:48px;text-align:center;color:var(--ikea-gray);">
                                No appointments yet.
                            </td>
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
            $(document).ready(function () {
                $('#appointmentsTable').DataTable({
                    pageLength: 15,
                    order: [[3, 'asc']],
                    columnDefs: [{ orderable: false, targets: [6] }],
                    language: {
                        search: 'Search appointments:',
                        info: 'Showing _START_–_END_ of _TOTAL_ appointments',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });
        </script>
    @endpush

</x-admin-layout>