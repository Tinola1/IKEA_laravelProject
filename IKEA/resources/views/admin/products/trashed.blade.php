@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="title">Deleted Products</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Deleted Products</h2>
                <p class="admin-page-subtitle">{{ $products->count() }} products in the trash.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">← Back to Products</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">
        <div class="admin-card" style="padding:var(--space-md);">
            @if($products->isEmpty())
                <p style="text-align:center;padding:var(--space-xl);color:var(--ikea-gray);">
                    No deleted products.
                </p>
            @else
            <table id="trashedTable" class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Deleted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
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
                        <td class="order-date">{{ $product->deleted_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-status-activate">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.products.force-delete', $product->id) }}"
                                      onsubmit="return confirm('Permanently delete {{ addslashes($product->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="btn-user-delete">Delete Forever</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#trashedTable').DataTable({
                    pageLength: 15,
                    order: [[5, 'desc']],
                    columnDefs: [{ orderable: false, targets: [1, 6] }],
                    language: { paginate: { previous: '←', next: '→' } },
                });
            });
        </script>
    @endpush

</x-admin-layout>