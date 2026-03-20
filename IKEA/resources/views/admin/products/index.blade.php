@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Products</h2>
                <p class="admin-page-subtitle">Manage your product catalogue.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">+ Add Product</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">

        {{-- FILTER BAR --}}
        <div class="admin-card" style="padding: 14px var(--space-md);">
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
            <table id="productsTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
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
                    <tr><td colspan="8" class="admin-empty-row">No products found.</td></tr>
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
                table = $('#productsTable').DataTable({
                    pageLength: 15,
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']],
                    columnDefs: [
                        { orderable: false, targets: [1, 7] },
                        { type: 'num', targets: [4, 5] },
                    ],
                    order: [[0, 'asc']],
                    language: {
                        search: 'Search products:',
                        lengthMenu: 'Show _MENU_ products',
                        info: 'Showing _START_–_END_ of _TOTAL_ products',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== 'productsTable') return true;
                var categoryFilter     = document.getElementById('categoryFilter').value.toLowerCase();
                var availabilityFilter = document.getElementById('availabilityFilter').value.toLowerCase();
                var rowCategory        = data[3].trim().toLowerCase();
                var rowAvailability    = data[6].trim().toLowerCase();
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