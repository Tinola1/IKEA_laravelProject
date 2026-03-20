@php use Illuminate\Support\Facades\Storage; @endphp
<x-admin-layout>
    <x-slot name="title">Edit Product — {{ $product->name }}</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Edit Product</h2>
                <p class="admin-page-subtitle">{{ $product->name }}</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">← Back to Products</a>
        </div>
    </x-slot>

    <div class="admin-content">
        @if(session('success'))
            <div class="admin-flash success" style="margin-bottom:var(--space-sm);">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="admin-flash error" style="margin-bottom:var(--space-sm);">
                {{ session('error') }}
            </div>
        @endif

        <div class="admin-card" style="padding:var(--space-md); max-width:720px;">
            <form method="POST" action="{{ route('admin.products.update', $product) }}"
                enctype="multipart/form-data" id="productForm"
                onsubmit="return confirmSave()">
                @csrf @method('PUT')

                {{-- Hidden inputs for staged deletions --}}
                <div id="stagedDeletions"></div>

                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category_id" class="form-input">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Product Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-input">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-input">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (₱) <span class="required">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="form-input">
                        @error('price')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock <span class="required">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="form-input">
                        @error('stock')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- PRIMARY IMAGE --}}
                <div class="form-group">
                    <label class="form-label">Primary Image</label>
                    <div class="extra-images-grid">
                        <div id="primaryPreviewWrap"
                             style="{{ $product->image ? 'position:relative;' : 'display:none;position:relative;' }}">
                            <img id="primaryImg"
                                 src="{{ $product->image ? Storage::url($product->image) : '' }}"
                                 class="extra-image-thumb">
                            <button type="button" class="extra-image-delete"
                                    title="Remove" onclick="removePrimary()">✕</button>
                        </div>
                        <label class="extra-image-add" id="primaryAddBtn"
                               title="Add primary image"
                               style="{{ $product->image ? 'display:none;' : '' }}">
                            <span>+</span>
                            <input type="file" name="image" accept="image/*"
                                   class="extra-image-input" id="primaryInput"
                                   onchange="previewPrimary(this)">
                        </label>
                    </div>
                    @error('image')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- ADDITIONAL PHOTOS --}}
                <div class="form-group">
                    <label class="form-label">Additional Photos</label>
                    <div class="extra-images-grid" id="extraImagesGrid">

                        @foreach($product->productImages as $img)
                        <div class="extra-image-wrap" id="imgWrap{{ $img->id }}">
                            <img src="{{ Storage::url($img->path) }}" class="extra-image-thumb">
                            <button type="button" class="extra-image-delete"
                                    title="Remove"
                                    onclick="stageDelete({{ $img->id }})">✕</button>
                        </div>
                        @endforeach

                        <div id="newImagePreviews" style="display:contents;"></div>

                        <label class="extra-image-add" title="Add photos">
                            <span>+</span>
                            <input type="file" name="extra_images[]" accept="image/*"
                                   multiple class="extra-image-input"
                                   id="extraInput" onchange="previewExtras(this)">
                        </label>
                    </div>
                    @error('extra_images.*')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                {{-- AVAILABILITY --}}
                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 0;">
                        <input type="checkbox" name="is_available" value="1" id="is_available"
                               {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                               style="width:18px;height:18px;cursor:pointer;flex-shrink:0;">
                        <span style="font-size:14px;color:var(--ikea-dark);">Available for purchase</span>
                    </label>
                    <p style="font-size:11px;color:var(--ikea-gray);margin-top:2px;">
                        Uncheck to hide this product from the shop.
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="admin-btn-primary">Save Changes</button>
                    <button type="button" class="admin-btn-ghost" onclick="revertChanges()">Revert Changes</button>
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-ghost">Cancel</a>
                </div>

            </form>
        </div>
    </div>
    
    <script>
        const stagedIds = [];
        const originalPrimaryUrl = "{{ $product->image ? Storage::url($product->image) : '' }}";
        const hasOriginalPrimary = {{ $product->image ? 'true' : 'false' }};

        function confirmSave() {
            const primaryVisible = document.getElementById('primaryPreviewWrap').style.display !== 'none';
            const primaryHasFile = document.getElementById('primaryInput').value !== '';

            if (!primaryVisible && !primaryHasFile) {
                return confirm('No primary image is set. Are you sure you want to save without one?');
            }
            return true;
        }

        function stageDelete(id) {
            document.getElementById('imgWrap' + id).classList.add('extra-image-staged');
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'delete_images[]';
            input.value = id;
            input.id    = 'deleteInput' + id;
            document.getElementById('stagedDeletions').appendChild(input);
            stagedIds.push(id);
        }

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
                            document.getElementById('extraInput').value = '';
                        }
                    };

                    wrap.appendChild(img);
                    wrap.appendChild(btn);
                    container.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        }

        function revertChanges() {
            // Unstage deletions
            stagedIds.forEach(id => {
                const wrap = document.getElementById('imgWrap' + id);
                if (wrap) wrap.classList.remove('extra-image-staged');
                const input = document.getElementById('deleteInput' + id);
                if (input) input.remove();
            });
            stagedIds.length = 0;

            // Clear new image previews only
            document.getElementById('newImagePreviews').innerHTML = '';
            document.getElementById('extraInput').value = '';

            // Restore original primary image state (don't touch text fields)
            document.getElementById('primaryInput').value = '';
            if (hasOriginalPrimary) {
                document.getElementById('primaryImg').src = originalPrimaryUrl;
                document.getElementById('primaryPreviewWrap').style.display = 'block';
                document.getElementById('primaryAddBtn').style.display = 'none';
            } else {
                document.getElementById('primaryPreviewWrap').style.display = 'none';
                document.getElementById('primaryAddBtn').style.display = 'flex';
            }

            // Show notice
            const existing = document.getElementById('revertNotice');
            if (existing) existing.remove();
            const notice = document.createElement('div');
            notice.id = 'revertNotice';
            notice.className = 'admin-flash success';
            notice.style.marginBottom = 'var(--space-sm)';
            notice.textContent = 'Image changes reverted.';
            document.querySelector('.admin-content').prepend(notice);
            setTimeout(() => notice.remove(), 3000);
        }
    </script>

</x-admin-layout>