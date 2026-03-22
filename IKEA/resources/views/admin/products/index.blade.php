@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="title">Products</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Products</h2>
                <p class="admin-page-subtitle">Manage your product catalogue.</p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('admin.products.trashed') }}" class="admin-btn-secondary">🗑 Trash</a>
                <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">+ Add Product</a>
            </div>
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
            @if(session('import_errors'))
                <ul style="margin:6px 0 0 16px;font-size:13px;">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="admin-content">

        {{-- STAT CARDS --}}
        @php
            $totalProducts    = $products->count();
            $availableCount   = $products->where('is_available', true)->count();
            $lowStockCount    = $products->where('stock', '<=', 5)->where('stock', '>', 0)->count();
            $outOfStockCount  = $products->where('stock', 0)->count();
        @endphp
        <div class="admin-stat-grid" style="grid-template-columns:repeat(4,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon products">📦</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Products</div>
                    <div class="admin-stat-value">{{ $totalProducts }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#e8f5e9;">✅</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Available</div>
                    <div class="admin-stat-value" style="color:#2e7d32;">{{ $availableCount }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fff3e0;">⚠️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Low Stock</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $lowStockCount }}</div>
                    <div class="admin-stat-meta">5 or fewer remaining</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#ffebee;">🚫</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Out of Stock</div>
                    <div class="admin-stat-value" style="color:#CC0008;">{{ $outOfStockCount }}</div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="admin-card" style="padding:14px var(--space-md);">
            <div class="orders-filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select id="categoryFilter" class="admin-select" onchange="filterProducts()">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Availability</label>
                    <select id="availabilityFilter" class="admin-select" onchange="filterProducts()">
                        <option value="">All</option>
                        <option value="yes">Available</option>
                        <option value="no">Unavailable</option>
                    </select>
                </div>
                <div class="filter-group" style="align-self:flex-end;">
                    <button onclick="clearFilters()" class="btn-clear-filters">Clear</button>
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
                <div style="display:flex;gap:8px;align-items:center;">
                    <a href="{{ route('admin.products.template') }}" class="btn-clear-filters">⬇ Download Import Template</a>
                    <button onclick="document.getElementById('importModal').style.display='flex'" class="btn-clear-filters">↑ Import Products from File</button>
                </div>
            </div>

            <table id="productsTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:36px;"><input type="checkbox" id="selectAll" style="cursor:pointer;"></th>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><input type="checkbox" class="row-check" value="{{ $product->id }}" style="cursor:pointer;"></td>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="admin-thumb">
                            @else
                                <span style="font-size:12px;color:var(--ikea-gray);">—</span>
                            @endif
                        </td>
                        <td class="table-product-name">{{ $product->name }}</td>
                        <td class="table-category">{{ $product->category->name }}</td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock == 0)
                                <span style="font-weight:700;color:#CC0008;">0</span>
                            @elseif($product->stock <= 5)
                                <span style="font-weight:700;color:#f57c00;">{{ $product->stock }}</span>
                            @else
                                {{ $product->stock }}
                            @endif
                        </td>
                        <td>
                            @if($product->is_available)
                                <span class="badge-yes">Yes</span>
                            @else
                                <span class="badge-no">No</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="admin-empty-row">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Import Modal --}}
    <div id="importModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:440px;margin:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:700;margin:0;">Import Products</h3>
                <button onclick="document.getElementById('importModal').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#767676;">×</button>
            </div>
            <p style="font-size:13px;color:#767676;margin-bottom:16px;">Upload a <strong>.xlsx</strong> or <strong>.csv</strong> file. First row must be the heading row. <a href="{{ route('admin.products.template') }}" style="color:var(--ikea-blue);">Download template</a> to see the required columns.</p>
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_file" accept=".xlsx,.xls,.csv"
                    style="width:100%;padding:10px;border:1.5px dashed var(--ikea-border);border-radius:8px;font-size:13px;margin-bottom:16px;cursor:pointer;">
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                            style="padding:10px 20px;border:1px solid var(--ikea-border);background:#fff;border-radius:6px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" class="admin-btn-primary">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            var table;
            $(document).ready(function () {
                table = $('#productsTable').DataTable({
                    pageLength: 15,
                    stateSave: true,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    columnDefs: [
                        { orderable: false, targets: [0, 2, 8] },
                        { type: 'num', targets: [5, 6] },
                    ],
                    order: [[1, 'asc']],
                    language: {
                        search: 'Search products:',
                        lengthMenu: 'Show _MENU_ products',
                        info: 'Showing _START_–_END_ of _TOTAL_ products',
                        paginate: { previous: '←', next: '→' },
                    },
                });

                $('#selectAll').on('change', function () {
                    table.$('.row-check').prop('checked', this.checked);
                    updateBulkBar();
                });

                $('#productsTable').on('change', '.row-check', function () {
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
                if (!confirm('Delete ' + ids.length + ' selected product(s)? This cannot be undone.')) return;
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.products.bulk-destroy') }}';
                form.innerHTML = '@csrf<input name="ids" value="' + ids.join(',') + '">';
                document.body.appendChild(form);
                form.submit();
            }

            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== 'productsTable') return true;
                var categoryFilter     = document.getElementById('categoryFilter').value.toLowerCase();
                var availabilityFilter = document.getElementById('availabilityFilter').value.toLowerCase();
                var rowCategory        = data[4].trim().toLowerCase();
                var rowAvailability    = data[7].trim().toLowerCase();
                if (categoryFilter     && rowCategory     !== categoryFilter)    return false;
                if (availabilityFilter && rowAvailability !== availabilityFilter) return false;
                return true;
            });

            function filterProducts() { if (table) table.draw(); }
            function clearFilters() {
                document.getElementById('categoryFilter').value     = '';
                document.getElementById('availabilityFilter').value = '';
                if (table) table.draw();
            }
        </script>
    @endpush

</x-admin-layout>