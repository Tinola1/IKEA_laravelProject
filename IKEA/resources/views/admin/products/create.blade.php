<x-admin-layout>
    <x-slot name="title">Add Product</x-slot>
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
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="createProductForm" novalidate>
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
                    <div class="extra-images-grid">
                        <div id="primaryPreviewWrap" style="display:none;position:relative;">
                            <img id="primaryImg" src="" class="extra-image-thumb">
                            <button type="button" class="extra-image-delete" title="Remove"
                                onclick="removePrimary()">✕</button>
                        </div>
                        <label class="extra-image-add" id="primaryAddBtn" title="Add primary image">
                            <span>+</span>
                            <input type="file" name="image" accept="image/*"
                                class="extra-image-input" id="primaryInput" onchange="previewPrimary(this)">
                        </label>
                    </div>
                    @error('image')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Additional Photos</label>
                    <div class="extra-images-grid" id="extraImagesGrid">
                        <label class="extra-image-add" id="extraAddBtn" title="Add photos">
                            <span>+</span>
                            <input type="file" name="extra_images[]" accept="image/*" multiple
                                class="extra-image-input" id="extraInput" onchange="previewExtras(this)">
                        </label>
                    </div>
                    @error('extra_images.*')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 0;">
                        <input type="checkbox" name="is_available" value="1" id="is_available"
                            {{ old('is_available', true) ? 'checked' : '' }}
                            style="width:18px;height:18px;cursor:pointer;flex-shrink:0;">
                        <span style="font-size:14px;color:var(--ikea-dark);">Available for purchase</span>
                    </label>
                    <p style="font-size:11px;color:var(--ikea-gray);margin-top:2px;">
                        Uncheck to hide this product from the shop.
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="admin-btn-primary">Save Product</button>
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function previewPrimary(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('primaryImg').src = e.target.result;
                    document.getElementById('primaryPreviewWrap').style.display = 'block';
                    document.getElementById('primaryAddBtn').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePrimary() {
            document.getElementById('primaryImg').src = '';
            document.getElementById('primaryPreviewWrap').style.display = 'none';
            document.getElementById('primaryAddBtn').style.display = 'flex';
            document.getElementById('primaryInput').value = '';
        }
        let extraFiles = [];

        function syncExtraInput() {
            const dt = new DataTransfer();
            extraFiles.forEach(f => dt.items.add(f));
            document.getElementById('extraInput').files = dt.files;
        }

        function previewExtras(input) {
            const grid = document.getElementById('extraImagesGrid');
            const addLabel = document.getElementById('extraAddBtn');

            Array.from(input.files).forEach(file => {
                // Skip duplicates (same name + size)
                if (extraFiles.some(f => f.name === file.name && f.size === file.size)) return;

                extraFiles.push(file);

                const reader = new FileReader();
                reader.onload = e => {
                    const wrap = document.createElement('div');
                    wrap.className = 'extra-image-wrap new-preview-wrap';
                    wrap.style.cssText = 'position:relative;width:72px;height:72px;flex-shrink:0;';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'extra-image-thumb';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'extra-image-delete';
                    btn.title = 'Remove';
                    btn.textContent = '✕';
                    btn.onclick = () => {
                        extraFiles = extraFiles.filter(f => f !== file);
                        wrap.remove();
                        syncExtraInput();
                    };

                    wrap.appendChild(img);
                    wrap.appendChild(btn);
                    grid.insertBefore(wrap, addLabel);
                };
                reader.readAsDataURL(file);
            });

            syncExtraInput();
        }
        
        document.getElementById('createProductForm').addEventListener('submit', function(e) {
            let valid = true;
            const errors = {};

            const name = document.querySelector('[name="name"]');
            const price = document.querySelector('[name="price"]');
            const stock = document.querySelector('[name="stock"]');
            const category = document.querySelector('[name="category_id"]');

            // Clear previous errors
            document.querySelectorAll('.js-error').forEach(el => el.remove());

            if (!category.value) {
                errors.category_id = 'Please select a category.';
                valid = false;
            }

            if (!name.value.trim()) {
                errors.name = 'Product name is required.';
                valid = false;
            } else if (name.value.trim().length > 255) {
                errors.name = 'Product name must not exceed 255 characters.';
                valid = false;
            }

            if (!price.value || parseFloat(price.value) < 0) {
                errors.price = 'Please enter a valid price.';
                valid = false;
            }

            if (!stock.value || parseInt(stock.value) < 0) {
                errors.stock = 'Please enter a valid stock quantity.';
                valid = false;
            }

            // Inject error messages
            Object.keys(errors).forEach(field => {
                const input = document.querySelector('[name="' + field + '"]');
                const msg = document.createElement('p');
                msg.className = 'form-error js-error';
                msg.textContent = errors[field];
                input.parentNode.appendChild(msg);
                input.style.borderColor = '#CC0008';
            });

            if (!valid) e.preventDefault();
        });

        // Clear red border on input
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                const err = this.parentNode.querySelector('.js-error');
                if (err) err.remove();
            });
        });
    </script>

</x-admin-layout>