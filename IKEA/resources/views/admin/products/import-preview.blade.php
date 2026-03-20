<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Import Preview</h2>
                <p class="admin-page-subtitle">Review the rows below before confirming.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-btn-primary" style="background:#fff;color:var(--ikea-blue);border:1.5px solid var(--ikea-blue);">← Back</a>
        </div>
    </x-slot>

    <div class="admin-content">

        {{-- SUMMARY STRIP --}}
        <div style="display:flex;gap:12px;margin-bottom:var(--space-md);">
            <div class="admin-card" style="padding:14px 20px;flex:1;border-left:4px solid #2e7d32;">
                <div style="font-size:12px;color:var(--ikea-gray);margin-bottom:2px;">Ready to import</div>
                <div style="font-size:24px;font-weight:900;color:#2e7d32;">{{ count($valid) }}</div>
            </div>
            <div class="admin-card" style="padding:14px 20px;flex:1;border-left:4px solid #CC0008;">
                <div style="font-size:12px;color:var(--ikea-gray);margin-bottom:2px;">Rows with errors</div>
                <div style="font-size:24px;font-weight:900;color:#CC0008;">{{ count($invalid) }}</div>
            </div>
        </div>

        {{-- VALID ROWS --}}
        @if(count($valid))
        <div class="admin-card" style="padding:var(--space-md);margin-bottom:var(--space-md);">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:#2e7d32;">✓ Valid rows — will be imported</h3>
            <table class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Row</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($valid as $row)
                    <tr style="background:#f0faf0;">
                        <td style="color:var(--ikea-gray);">{{ $row['row'] }}</td>
                        <td class="table-product-name">{{ $row['name'] }}</td>
                        <td class="table-category">{{ $row['category'] }}</td>
                        <td>₱{{ number_format($row['price'], 2) }}</td>
                        <td>{{ $row['stock'] }}</td>
                        <td style="font-size:12px;color:var(--ikea-gray);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $row['description'] ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- INVALID ROWS --}}
        @if(count($invalid))
        <div class="admin-card" style="padding:var(--space-md);margin-bottom:var(--space-md);">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:#CC0008;">✗ Rows with errors — will be skipped</h3>
            <table class="admin-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Row</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Errors</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invalid as $row)
                    <tr style="background:#fff5f5;">
                        <td style="color:var(--ikea-gray);">{{ $row['row'] }}</td>
                        <td>{{ $row['name'] ?: '—' }}</td>
                        <td>{{ $row['category'] ?: '—' }}</td>
                        <td>{{ $row['price'] ?: '—' }}</td>
                        <td>{{ $row['stock'] ?: '—' }}</td>
                        <td style="color:#CC0008;font-size:12px;">{{ implode(', ', $row['errors']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- CONFIRM / CANCEL --}}
        @if(count($valid))
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('admin.products.index') }}"
               style="padding:10px 24px;border:1px solid var(--ikea-border);background:#fff;border-radius:6px;font-size:14px;font-weight:700;text-decoration:none;color:var(--ikea-dark);">
                Cancel
            </a>
            <form action="{{ route('admin.products.confirm') }}" method="POST">
                @csrf
                <button type="submit" class="admin-btn-primary">
                    Confirm & Import {{ count($valid) }} product(s)
                </button>
            </form>
        </div>
        @else
        <div style="text-align:center;padding:24px;">
            <p style="color:var(--ikea-gray);margin-bottom:16px;">No valid rows to import. Fix the errors above and re-upload.</p>
            <a href="{{ route('admin.products.index') }}" class="admin-btn-primary">← Back to Products</a>
        </div>
        @endif

    </div>
</x-admin-layout>