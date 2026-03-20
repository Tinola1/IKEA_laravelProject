<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Categories</h2>
                <p class="admin-page-subtitle">Manage your product categories.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="admin-btn-primary">+ Add Category</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="admin-flash" style="margin:var(--space-md) var(--space-lg) 0;background:#fff0f0;border-left:4px solid #CC0008;padding:12px 16px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="admin-content">

        {{-- STAT CARDS --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon products">🏷️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Categories</div>
                    <div class="admin-stat-value">{{ $categories->count() }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e8f5e9;">📦</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Products</div>
                    <div class="admin-stat-value" style="color:#2e7d32;">{{ $totalProducts }}</div>
                    <div class="admin-stat-meta">across all categories</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fff3e0;">⚠️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Empty Categories</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $categories->where('products_count', 0)->count() }}</div>
                    <div class="admin-stat-meta">no products assigned</div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="admin-card" style="padding:var(--space-md);">

            {{-- TABLE TOOLBAR --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;gap:8px;align-items:center;">
                    <button onclick="location.reload()" class="btn-clear-filters">↻ Refresh</button>
                    <button id="btnBulkDelete" onclick="bulkDelete()" style="display:none;height:36px;padding:0 16px;background:#CC0008;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;">
                        🗑 Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>

            <table id="categoriesTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:36px;"><input type="checkbox" id="selectAll" style="cursor:pointer;"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td><input type="checkbox" class="row-check" value="{{ $category->id }}" style="cursor:pointer;"></td>
                        <td>{{ $category->id }}</td>
                        <td class="table-product-name">{{ $category->name }}</td>
                        <td style="font-size:12px;color:var(--ikea-gray);font-family:monospace;">{{ $category->slug }}</td>
                        <td style="font-size:13px;color:var(--ikea-gray);max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $category->description ?? '—' }}
                        </td>
                        <td>
                            <span style="font-weight:700;{{ $category->products_count === 0 ? 'color:#f57c00;' : 'color:#2e7d32;' }}">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ addslashes($category->name) }}? Products in this category will be unassigned.')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="admin-empty-row">No categories found.</td></tr>
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
                table = $('#categoriesTable').DataTable({
                    pageLength: 15,
                    stateSave: true,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    columnDefs: [
                        { orderable: false, targets: [0, 6] },
                        { type: 'num', targets: [1, 5] },
                    ],
                    order: [[1, 'asc']],
                    language: {
                        search: 'Search categories:',
                        lengthMenu: 'Show _MENU_ categories',
                        info: 'Showing _START_–_END_ of _TOTAL_ categories',
                        paginate: { previous: '←', next: '→' },
                    },
                });

                $('#selectAll').on('change', function () {
                    table.$('.row-check').prop('checked', this.checked);
                    updateBulkBar();
                });

                $('#categoriesTable').on('change', '.row-check', function () {
                    updateBulkBar();
                    $('#selectAll').prop('checked', table.$('.row-check:not(:checked)').length === 0);
                });
            });

            function updateBulkBar() {
                var count = table.$('.row-check:checked').length;
                document.getElementById('selectedCount').textContent = count;
                document.getElementById('btnBulkDelete').style.display = count > 0 ? 'inline-flex' : 'none';
            }

            function bulkDelete() {
                var ids = table.$('.row-check:checked').map(function () { return this.value; }).get();
                if (!ids.length) return;
                if (!confirm('Delete ' + ids.length + ' selected category/categories? This cannot be undone.')) return;
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.categories.bulk-destroy') }}';
                form.innerHTML = '@csrf<input name="ids" value="' + ids.join(',') + '">';
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    @endpush

</x-admin-layout>