<div class="form-row">
    <div class="form-group">
        <label class="form-label">Label <span class="required">*</span></label>
        <select name="label" class="form-input">
            @foreach(['Home','Office','Other'] as $opt)
                <option value="{{ $opt }}" {{ old('label', $editing ?? false ? '' : 'Home') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Recipient Name <span class="required">*</span></label>
        <input type="text" name="full_name" class="form-input" value="{{ old('full_name') }}" required>
        @error('full_name')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label">Phone <span class="required">*</span></label>
    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" required>
    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label class="form-label">Street Address <span class="required">*</span></label>
    <input type="text" name="address" class="form-input" value="{{ old('address') }}" required>
    @error('address')<p class="form-error">{{ $message }}</p>@enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">City <span class="required">*</span></label>
        <input type="text" name="city" class="form-input" value="{{ old('city') }}" required>
        @error('city')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Province <span class="required">*</span></label>
        <input type="text" name="province" class="form-input" value="{{ old('province') }}" required>
        @error('province')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">ZIP Code <span class="required">*</span></label>
        <input type="text" name="zip_code" class="form-input" value="{{ old('zip_code') }}" required>
        @error('zip_code')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-group" style="display:flex;align-items:center;gap:8px;padding-top:28px;">
        <input type="checkbox" name="is_default" id="is_default_{{ $editing ?? 'new' }}" value="1" style="width:16px;height:16px;cursor:pointer;">
        <label for="is_default_{{ $editing ?? 'new' }}" class="form-label" style="margin:0;cursor:pointer;">Set as default address</label>
    </div>
</div>