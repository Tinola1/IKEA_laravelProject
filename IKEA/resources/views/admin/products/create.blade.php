<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Add Product</h2>
                <p class="admin-page-subtitle">Create a new product listing.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">← Back to Products</a>
        </div>
    </x-slot>

    <div class="admin-content">
        <div class="admin-card" style="padding:var(--space-md); max-width:720px;">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category_id" class="form-input">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Product Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="e.g. KALLAX Shelf Unit">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="Brief product description...">{{ old('description') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (₱) <span class="required">*</span></label>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" class="form-input" placeholder="0.00">
                        @error('price')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock <span class="required">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock') }}" min="0" class="form-input" placeholder="0">
                        @error('stock')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Primary Image</label>
                    <input type="file" name="image" accept="image/*" class="form-file" onchange="previewImage(this)">
                    <div id="imagePreview" style="display:none;margin-top:8px;">
                        <img id="previewImg" src="" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--ikea-border);">
                    </div>
                    @error('image')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" name="is_available" value="1" id="is_available" {{ old('is_available', true) ? 'checked' : '' }}>
                    <label for="is_available" class="form-label">Available for purchase</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="admin-btn-primary">Save Product</button>
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</x-admin-layout>