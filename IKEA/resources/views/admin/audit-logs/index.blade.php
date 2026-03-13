<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Audit Logs</h2>
                <p class="admin-page-subtitle">All actions performed on the system</p>
            </div>
            <div class="admin-header-actions">
                <span style="font-size:var(--text-sm);color:var(--ikea-gray);font-weight:600;">
                    Last updated: {{ now()->format('M d, Y H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="admin-dashboard">

        {{-- ── FILTER BAR ──────────────────────────────────────── --}}
        <div class="admin-card" style="padding:var(--space-md);">
            <div class="audit-filters">
                <div class="audit-filter-group">
                    <label class="admin-label">Filter by Action</label>
                    <select class="admin-select" id="actionFilter" onchange="filterLogs()">
                        <option value="">All Actions</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="order_placed">Order Placed</option>
                        <option value="order_cancelled">Order Cancelled</option>
                    </select>
                </div>
                <div class="audit-filter-group">
                    <label class="admin-label">Filter by User</label>
                    <select class="admin-select" id="userFilter" onchange="filterLogs()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="audit-filter-group">
                    <label class="admin-label">Date From</label>
                    <input type="date" class="admin-input" id="dateFrom"
                           style="height:38px;padding:0 10px;"
                           onchange="filterLogs()">
                </div>
                <div class="audit-filter-group">
                    <label class="admin-label">Date To</label>
                    <input type="date" class="admin-input" id="dateTo"
                           style="height:38px;padding:0 10px;"
                           onchange="filterLogs()">
                </div>
                <div class="audit-filter-group" style="justify-content:flex-end;align-self:flex-end;">
                    <button class="admin-btn-secondary" onclick="clearFilters()" style="height:38px;padding:0 16px;">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        {{-- ── LOG TABLE ───────────────────────────────────────── --}}
        <div class="admin-card" style="padding:var(--space-md);">

            @if($logs->isEmpty())
                <div class="admin-empty" style="padding:var(--space-2xl);">
                    <span style="font-size:48px;">📋</span>
                    <p style="font-size:var(--text-md);font-weight:700;color:var(--ikea-dark);margin-top:8px;">
                        No audit logs yet
                    </p>
                    <p>Actions performed on the system will appear here once logging is enabled.</p>
                </div>
            @else
                <table id="auditTable" class="admin-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>Subject</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $actionCss = match(true) {
                                    str_contains($log->action, 'delete') || str_contains($log->action, 'cancel') => 'status-cancelled',
                                    str_contains($log->action, 'create') || str_contains($log->action, 'placed') => 'status-completed',
                                    str_contains($log->action, 'login')  => 'status-processing',
                                    default => 'status-pending',
                                };
                            @endphp
                            <tr>
                                <td class="order-date" style="white-space:nowrap;">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    <span style="font-size:11px;color:var(--ikea-gray);">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div class="user-avatar" style="width:28px;height:28px;font-size:12px;">
                                            {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13px;">{{ $log->user?->name ?? 'System' }}</div>
                                            <div style="font-size:11px;color:var(--ikea-gray);">{{ $log->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-chip-sm">
                                        {{ ucfirst($log->user?->getRoleNames()->first() ?? '—') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="order-status-badge {{ $actionCss }}">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td style="font-weight:600;font-size:13px;">{{ $log->subject_type ?? '—' }}</td>
                                <td style="font-size:12px;color:var(--ikea-gray);max-width:200px;">
                                    {{ Str::limit($log->description ?? '—', 60) }}
                                </td>
                                <td style="font-size:12px;color:var(--ikea-gray);font-family:monospace;">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        {{-- Coming soon notice --}}
        <div class="audit-coming-soon">
            <span>🔧</span>
            <div>
                <strong>Full audit logging coming soon.</strong>
                Install <code>spatie/laravel-activitylog</code> to automatically track all model
                changes, user logins, and admin actions across the system.
            </div>
        </div>

    </div>

    <style>
        .audit-filters {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: var(--space-sm);
            align-items: end;
        }
        .audit-filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--ikea-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            flex-shrink: 0;
        }
        .audit-coming-soon {
            display: flex;
            align-items: flex-start;
            gap: var(--space-sm);
            background: #fff8e1;
            border: 1.5px solid #ffe082;
            border-radius: 8px;
            padding: var(--space-md);
            font-size: var(--text-sm);
            color: #5d4037;
            line-height: 1.6;
        }
        .audit-coming-soon span { font-size: 24px; flex-shrink: 0; }
        .audit-coming-soon code {
            background: rgba(0,0,0,0.08);
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        @media (max-width: 1100px) { .audit-filters { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 700px)  { .audit-filters { grid-template-columns: 1fr 1fr; } }
    </style>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            @if(!$logs->isEmpty())
            $(document).ready(function () {
                window._table = $('#auditTable').DataTable({
                    pageLength: 25,
                    order: [[0, 'desc']],
                    columnDefs: [{ orderable: false, targets: [6] }],
                    language: {
                        search: 'Search logs:',
                        info: 'Showing _START_–_END_ of _TOTAL_ entries',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });

            function filterLogs() {
                // Placeholder — wire to backend filters when activitylog is installed
                console.log('Filter applied — backend filtering not yet implemented.');
            }

            function clearFilters() {
                document.getElementById('actionFilter').value = '';
                document.getElementById('userFilter').value   = '';
                document.getElementById('dateFrom').value     = '';
                document.getElementById('dateTo').value       = '';
            }
            @else
            function filterLogs() {}
            function clearFilters() {}
            @endif
        </script>
    @endpush

</x-admin-layout>