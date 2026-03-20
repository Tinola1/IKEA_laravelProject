<x-admin-layout>
    <x-slot name="title">Edit Category</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Edit Category</h2>
                <p class="admin-page-subtitle">Editing: {{ $category->name }}</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn-primary" style="background:#fff;color:var(--ikea-blue);border:1.5px solid var(--ikea-blue);">← Back</a>
        </div>
    </x-slot>

    <div class="admin-content">
        <div class="admin-card" style="padding:var(--space-lg);max-width:600px;">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:20px;">
                    <label class="filter-label" style="display:block;margin-bottom:6px;">Name <span style="color:#CC0008;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                           class="admin-select" style="width:100%;height:40px;padding:0 12px;">
                    @error('name')
                        <p style="color:#CC0008;font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label class="filter-label" style="display:block;margin-bottom:6px;">Slug</label>
                    <input type="text" value="{{ $category->slug }}" disabled
                           class="admin-select" style="width:100%;height:40px;padding:0 12px;background:#f5f5f0;color:var(--ikea-gray);">
                    <p style="font-size:11px;color:var(--ikea-gray);margin-top:4px;">Auto-generated from name. Updates on save.</p>
                </div>

                <div style="margin-bottom:24px;">
                    <label class="filter-label" style="display:block;margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="3"
                              class="admin-select" style="width:100%;padding:10px 12px;height:auto;resize:vertical;">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="admin-btn-primary">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}"
                       style="padding:10px 20px;border:1px solid var(--ikea-border);background:#fff;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none;color:var(--ikea-dark);">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>