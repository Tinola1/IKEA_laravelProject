<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Add Category</h2>
                <p class="admin-page-subtitle">Create a new product category.</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn-primary" style="background:#fff;color:var(--ikea-blue);border:1.5px solid var(--ikea-blue);">← Back</a>
        </div>
    </x-slot>

    <div class="admin-content">
        <div class="admin-card" style="padding:var(--space-lg);max-width:600px;">
            <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm" novalidate>
                @csrf

                <div style="margin-bottom:20px;">
                    <label class="filter-label" style="display:block;margin-bottom:6px;">Name <span style="color:#CC0008;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="admin-select" style="width:100%;height:40px;padding:0 12px;"
                           placeholder="e.g. Sofas & Armchairs">
                    @error('name')
                        <p style="color:#CC0008;font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:24px;">
                    <label class="filter-label" style="display:block;margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="3"
                              class="admin-select" style="width:100%;padding:10px 12px;height:auto;resize:vertical;"
                              placeholder="Optional — describe what products belong here.">{{ old('description') }}</textarea>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="admin-btn-primary">Save Category</button>
                    <a href="{{ route('admin.categories.index') }}"
                       style="padding:10px 20px;border:1px solid var(--ikea-border);background:#fff;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none;color:var(--ikea-dark);">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        document.querySelectorAll('.js-error').forEach(el => el.remove());

        const name = document.querySelector('[name="name"]');
        if (!name.value.trim()) {
            e.preventDefault();
            name.style.borderColor = '#CC0008';
            const msg = document.createElement('p');
            msg.className = 'form-error js-error';
            msg.textContent = 'Category name is required.';
            name.parentNode.appendChild(msg);
        }
    });

    document.querySelector('[name="name"]').addEventListener('input', function() {
        this.style.borderColor = '';
        const err = this.parentNode.querySelector('.js-error');
        if (err) err.remove();
    });
    </script>
</x-admin-layout>