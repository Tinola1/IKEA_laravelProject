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
                    <div class="extra-images-grid">
                        <div id="newImagePreviews" style="display:contents;"></div>
                        <label class="extra-image-add" title="Add photos">
                            <span>+</span>
                            <input type="file" name="extra_images[]" accept="image/*" multiple
                                class="extra-image-input" onchange="previewExtras(this)">
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

    <style>
        .extra-images-grid { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start; }
        .extra-image-add {
            width: 72px; height: 72px; border-radius: 6px;
            border: 2px dashed var(--ikea-border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--ikea-gray);
            font-size: 28px; font-weight: 300;
            transition: border-color .15s, color .15s;
            flex-shrink: 0;
        }
        .extra-image-add:hover { border-color: var(--ikea-blue); color: var(--ikea-blue); }
        .extra-image-input { display: none; }
        .extra-image-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 6px; border: 1px solid var(--ikea-border); display: block; }
        .extra-image-delete {
            position: absolute; top: -6px; right: -6px;
            width: 20px; height: 20px; border-radius: 50%;
            background: #CC0008; color: white; border: 2px solid white;
            font-size: 10px; font-weight: 900; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            padding: 0;
        }
    </style>

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
        function previewExtras(input) {
            const container = document.getElementById('newImagePreviews');
            container.innerHTML = '';
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const wrap = document.createElement('div');
                    wrap.style.cssText = 'position:relative;';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'extra-image-thumb';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'extra-image-delete';
                    btn.title = 'Remove';
                    btn.textContent = '✕';
                    btn.onclick = () => {
                        wrap.remove();
                        if (!document.getElementById('newImagePreviews').children.length) {
                            document.querySelector('.extra-image-input').value = '';
                        }
                    };

                    wrap.appendChild(img);
                    wrap.appendChild(btn);
                    container.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>

</x-admin-layout>