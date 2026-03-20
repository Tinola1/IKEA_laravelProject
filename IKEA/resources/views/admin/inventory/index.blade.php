@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Inventory</h2>
                <p class="admin-page-subtitle">Monitor and adjust product stock levels.</p>
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
                <div class="admin-stat-icon products">📦</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Products</div>
                    <div class="admin-stat-value">{{ $products->count() }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#fff3e0;">⚠️</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Low Stock</div>
                    <div class="admin-stat-value" style="color:#f57c00;">{{ $lowStock }}</div>
                    <div class="admin-stat-meta">5 or fewer remaining</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#ffebee;">🚫</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Out of Stock</div>
                    <div class="admin-stat-value" style="color:#CC0008;">{{ $outOfStock }}</div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="admin-card" style="padding:14px var(--space-md);">
            <div class="orders-filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select id="categoryFilter" class="admin-select" onchange="filterInventory()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Stock Status</label>
                    <select id="stockFilter" class="admin-select" onchange="filterInventory()">
                        <option value="">All</option>
                        <option value="out">Out of Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="ok">In Stock</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Availability</label>
                    <select id="availabilityFilter" class="admin-select" onchange="filterInventory()">
                        <option value="">All</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                <div class="filter-group" style="align-self:flex-end;">
                    <button onclick="clearFilters()" class="btn-clear-filters">Clear</button>
                </div>
            </div>
        </div>

        {{-- INVENTORY TABLE --}}
        <div class="admin-card" style="padding:var(--space-md);">

            {{-- TABLE TOOLBAR --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;gap:8px;align-items:center;">
                    <button onclick="location.reload()" class="btn-clear-filters">↻ Refresh</button>
                </div>
            </div>

            <table id="inventoryTable" class="admin-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Adjust Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
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
                        <td data-stock="{{ $product->stock }}">
                            @if($product->stock == 0)
                                <span style="font-weight:700;color:#CC0008;">0 — Out of Stock</span>
                            @elseif($product->stock <= 5)
                                <span style="font-weight:700;color:#f57c00;">{{ $product->stock }} — Low Stock</span>
                            @else
                                <span style="font-weight:700;color:#2e7d32;">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_available)
                                <span class="badge-yes">Available</span>
                            @else
                                <span class="badge-no">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.inventory.update', $product) }}" method="POST" class="inline-form">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="stock" value="{{ $product->stock }}" min="0" class="stock-input">
                                <button type="submit" class="btn-edit">Update</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="admin-empty-row">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <style>
        .inline-form { display:flex; align-items:center; gap:8px; }
        .stock-input {
            width: 72px;
            border: 1px solid var(--ikea-border);
            border-radius: 6px;
            padding: 5px 8px;
            text-align: center;
            font-size: 13px;
            font-family: 'Noto Sans', sans-serif;
        }
    </style>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            var table;

            $(document).ready(function () {
                table = $('#inventoryTable').DataTable({
                    pageLength: 15,
                    stateSave: true,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    columnDefs: [
                        { orderable: false, targets: [1, 7] },
                        { type: 'num', targets: [0, 4] },
                    ],
                    order: [[5, 'asc']],
                    language: {
                        search: 'Search products:',
                        lengthMenu: 'Show _MENU_ products',
                        info: 'Showing _START_–_END_ of _TOTAL_ products',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'inventoryTable') return true;

                var categoryFilter     = document.getElementById('categoryFilter').value.toLowerCase();
                var stockFilter        = document.getElementById('stockFilter').value;
                var availabilityFilter = document.getElementById('availabilityFilter').value.toLowerCase();

                var rowCategory     = data[3].trim().toLowerCase();
                var rowAvailability = data[6].trim().toLowerCase();

                var stockCell = table.row(dataIndex).node().querySelector('td[data-stock]');
                var rawStock  = stockCell ? parseInt(stockCell.getAttribute('data-stock')) : 0;

                if (categoryFilter && rowCategory !== categoryFilter) return false;

                if (stockFilter) {
                    if (stockFilter === 'out' && rawStock !== 0)              return false;
                    if (stockFilter === 'low' && !(rawStock > 0 && rawStock <= 5)) return false;
                    if (stockFilter === 'ok'  && rawStock <= 5)               return false;
                }

                if (availabilityFilter && rowAvailability !== availabilityFilter) return false;

                return true;
            });

            function filterInventory() { if (table) table.draw(); }

            function clearFilters() {
                document.getElementById('categoryFilter').value     = '';
                document.getElementById('stockFilter').value        = '';
                document.getElementById('availabilityFilter').value = '';
                if (table) table.draw();
            }
        </script>
    @endpush

</x-admin-layout>