<x-admin-layout>
    <x-slot name="title">Users</x-slot>
    <x-slot name="header">
       <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">User Management</h2>
                <p class="admin-page-subtitle">{{ $users->total() }} registered accounts</p>
            </div>
            <button onclick="document.getElementById('createUserModal').style.display='flex'"
                    class="admin-btn-primary">
                + Create User
            </button>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="admin-flash error" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('error') }}
        </div>
    @endif

    <div class="admin-dashboard">

        {{-- STAT STRIP --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(4,1fr);">
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
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#ffebee;">🚫</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Inactive</div>
                    <div class="admin-stat-value" style="color:#CC0008;">{{ $inactiveCount }}</div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="admin-card" style="padding:var(--space-md);">
            <table id="usersTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $role    = $user->getRoleNames()->first() ?? 'customer';
                            $isSelf  = $user->id === auth()->id();
                        @endphp
                        <tr>
                            <td class="order-id">#{{ $user->id }}</td>

                            {{-- Name --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="table-product-name">{{ $user->name }}</div>
                                        @if($isSelf)
                                            <div style="font-size:10px;color:var(--ikea-blue);font-weight:700;">YOU</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td style="color:var(--ikea-gray);font-size:13px;">{{ $user->email }}</td>

                            {{-- Role — dropdown inline --}}
                            <td>
                                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                    @csrf @method('PATCH')
                                    <select name="role"
                                            class="admin-select"
                                            onchange="this.form.submit()"
                                            style="height:30px;font-size:11px;padding:0 6px;">
                                        <option value="customer" @selected($role === 'customer')>Customer</option>
                                        <option value="staff"    @selected($role === 'staff')>Staff</option>
                                        <option value="admin"    @selected($role === 'admin')>Admin</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($user->is_active)
                                    <span class="badge-yes">Active</span>
                                @else
                                    <span class="badge-no">Inactive</span>
                                @endif
                            </td>

                            {{-- Orders --}}
                            <td style="font-weight:700;">{{ $user->orders_count ?? 0 }}</td>

                            {{-- Joined --}}
                            <td class="order-date">{{ $user->created_at->format('M d, Y') }}</td>

                            {{-- Actions --}}
                            <td>
                                @if($isSelf)
                                    <span style="font-size:11px;color:var(--ikea-gray);font-style:italic;">—</span>
                                @else
                                    <div style="display:flex;gap:6px;align-items:center;">

                                        {{-- Activate / Deactivate --}}
                                        <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="{{ $user->is_active ? 'btn-status-deactivate' : 'btn-status-activate' }}"
                                                    onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} {{ addslashes($user->name) }}?')">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-user-delete">Delete</button>
                                        </form>

                                    </div>
                                @endif
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
                    columnDefs: [{ orderable: false, targets: [3, 7] }],
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
    {{-- Create User Modal --}}
    <div id="createUserModal"
        onclick="if(event.target===this)this.style.display='none'"
        style="display:none;position:fixed;inset:0;z-index:999;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
        <div style="background:white;border-radius:12px;padding:var(--space-md);width:100%;max-width:480px;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-md);">
                <h3 style="font-size:var(--text-lg);font-weight:800;color:var(--ikea-dark);">Create New User</h3>
                <button onclick="document.getElementById('createUserModal').style.display='none'"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ikea-gray);">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm" novalidate>
                @csrf

                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input"
                        placeholder="e.g. Juan dela Cruz" value="{{ old('name') }}">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="form-input"
                        placeholder="user@example.com" value="{{ old('email') }}">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-input"
                            placeholder="Min. 8 characters">
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role" class="form-input">
                            <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="staff"    {{ old('role') === 'staff'    ? 'selected' : '' }}>Staff</option>
                            <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="display:flex;gap:8px;margin-top:var(--space-md);padding-top:var(--space-md);border-top:1px solid var(--ikea-border);">
                    <button type="submit" class="admin-btn-primary">Create User</button>
                    <button type="button"
                            onclick="document.getElementById('createUserModal').style.display='none'"
                            class="admin-btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Re-open modal if there are validation errors
    @if($errors->any())
        document.getElementById('createUserModal').style.display = 'flex';
    @endif

    // Client-side validation
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        let valid = true;
        this.querySelectorAll('.js-error').forEach(el => el.remove());

        const name     = this.querySelector('[name="name"]');
        const email    = this.querySelector('[name="email"]');
        const password = this.querySelector('[name="password"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name.value.trim()) {
            valid = false; showModalError(name, 'Full name is required.');
        }
        if (!email.value.trim()) {
            valid = false; showModalError(email, 'Email is required.');
        } else if (!emailRegex.test(email.value.trim())) {
            valid = false; showModalError(email, 'Please enter a valid email.');
        }
        if (!password.value) {
            valid = false; showModalError(password, 'Password is required.');
        } else if (password.value.length < 8) {
            valid = false; showModalError(password, 'Password must be at least 8 characters.');
        }

        if (!valid) e.preventDefault();
    });

    function showModalError(input, message) {
        input.style.borderColor = '#CC0008';
        const msg = document.createElement('p');
        msg.className = 'form-error js-error';
        msg.textContent = message;
        input.parentNode.appendChild(msg);
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const err = this.parentNode.querySelector('.js-error');
            if (err) err.remove();
        }, { once: true });
    }
    </script>
</x-admin-layout>