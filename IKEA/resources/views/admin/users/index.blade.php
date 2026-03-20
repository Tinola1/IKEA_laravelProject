<x-admin-layout>
    <x-slot name="title">Users</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">User Management</h2>
                <p class="admin-page-subtitle">{{ $users->total() }} registered accounts</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-dashboard">

        {{-- ── STAT STRIP ──────────────────────────────────────── --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon customers">👥</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Users</div>
                    <div class="admin-stat-value">{{ $users->total() }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon orders">🛍️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Customers</div>
                    <div class="admin-stat-value">{{ $customerCount }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon revenue">🔑</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Admins & Staff</div>
                    <div class="admin-stat-value">{{ $staffCount }}</div>
                </div>
            </div>
        </div>

        {{-- ── TABLE ───────────────────────────────────────────── --}}
        <div class="admin-card" style="padding:var(--space-md);">

            <table id="usersTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $role     = $user->getRoleNames()->first() ?? 'customer';
                            $roleCss  = match($role) {
                                'admin'  => 'status-processing',
                                'staff'  => 'status-completed',
                                default  => 'category-chip-sm',
                            };
                        @endphp
                        <tr>
                            <td class="order-id">#{{ $user->id }}</td>

                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="table-product-name">{{ $user->name }}</div>
                                </div>
                            </td>

                            <td style="color:var(--ikea-gray);font-size:13px;">{{ $user->email }}</td>

                            <td>
                                <span class="order-status-badge {{ $roleCss }}">
                                    {{ ucfirst($role) }}
                                </span>
                            </td>

                            <td style="font-weight:700;">{{ $user->orders_count ?? 0 }}</td>

                            <td class="order-date">{{ $user->created_at->format('M d, Y') }}</td>

                            <td>
                                <div class="table-actions">
                                    {{-- Role change form --}}
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role"
                                                class="admin-select"
                                                onchange="this.form.submit()"
                                                style="height:30px;font-size:11px;padding:0 6px;">
                                            <option value="customer" @selected($role === 'customer')>Customer</option>
                                            <option value="staff"    @selected($role === 'staff')>Staff</option>
                                            <option value="admin"    @selected($role === 'admin')>Admin</option>
                                        </select>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
                $('#usersTable').DataTable({
                    pageLength: 15,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    columnDefs: [{ orderable: false, targets: [6] }],
                    order: [[0, 'desc']],
                    language: {
                        search: 'Search users:',
                        lengthMenu: 'Show _MENU_ users',
                        info: 'Showing _START_–_END_ of _TOTAL_ users',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });
        </script>
    @endpush

</x-admin-layout>