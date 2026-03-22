<x-app-layout>
    <x-slot name="title">Profile Settings</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Profile Settings</h2>
                <p class="admin-page-subtitle">Manage your account, delivery address, and payment preferences.</p>
            </div>
        </div>
    </x-slot>

    <div class="profile-page">

        {{-- ── SUCCESS FLASH ───────────────────────────────────────── --}}
        @if(session('status') === 'profile-updated')
            <div class="profile-flash success">
                ✅ Profile updated successfully.
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="profile-flash success">
                ✅ Password updated successfully.
            </div>
        @endif

        <form method="POST"
              action="{{ route('profile.update') }}"
              enctype="multipart/form-data"
              class="profile-grid">
            @csrf
            @method('PATCH')

            {{-- ══════════════════════════════════════════════════════
                 LEFT COLUMN
            ══════════════════════════════════════════════════════ --}}
            <div class="profile-main">

                {{-- ── PROFILE PHOTO ──────────────────────────────── --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Profile Photo</h3>

                    <div class="avatar-section">
                        <div class="avatar-preview-wrap">
                            <img id="avatarPreview"
                                 src="{{ $user->avatarUrl() }}"
                                 alt="{{ $user->name }}"
                                 class="avatar-preview">
                            <label for="avatar" class="avatar-edit-btn" title="Change photo">
                                📷
                            </label>
                        </div>
                        <div class="avatar-info">
                            <div class="avatar-name">{{ $user->name }}</div>
                            <div class="avatar-email">{{ $user->email }}</div>
                            <div class="avatar-role">
                                {{ ucfirst($user->getRoleNames()->first() ?? 'Customer') }}
                            </div>
                            <label for="avatar" class="avatar-upload-label">
                                Change Photo
                            </label>
                            <input type="file"
                                   id="avatar"
                                   name="avatar"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   class="avatar-input"
                                   onchange="previewAvatar(this)">
                            <p class="avatar-hint">JPG, PNG or WEBP · Max 2MB</p>
                            @error('avatar')
                                <span class="profile-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── BASIC INFORMATION ───────────────────────────── --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Basic Information</h3>

                    <div class="profile-two-col">
                        <div class="profile-field">
                            <label class="profile-label" for="name">Full Name</label>
                            <input id="name" name="name" type="text"
                                   class="profile-input @error('name') is-error @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')<span class="profile-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="profile-field">
                            <label class="profile-label" for="email">Email Address</label>
                            <input id="email" name="email" type="email"
                                   class="profile-input @error('email') is-error @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')<span class="profile-error">{{ $message }}</span>@enderror
                            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                <span class="profile-unverified">⚠️ Email not verified.</span>
                            @endif
                        </div>
                    </div>

                    <div class="profile-field">
                        <label class="profile-label" for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="text"
                               class="profile-input @error('phone') is-error @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+63 912 345 6789">
                        @error('phone')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- ── DELIVERY ADDRESS ────────────────────────────── --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">
                        Delivery Address
                        @if($user->hasCompleteAddress())
                            <span class="profile-badge-complete">✓ Complete</span>
                        @else
                            <span class="profile-badge-incomplete">Incomplete</span>
                        @endif
                    </h3>
                    <p class="profile-section-hint">
                        This address will be pre-filled at checkout to save you time.
                    </p>

                    <div class="profile-field">
                        <label class="profile-label" for="address">Street Address</label>
                        <input id="address" name="address" type="text"
                               class="profile-input @error('address') is-error @enderror"
                               value="{{ old('address', $user->address) }}"
                               placeholder="e.g. 123 Rizal Street, Barangay San Antonio">
                        @error('address')<span class="profile-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="profile-three-col">
                        <div class="profile-field">
                            <label class="profile-label" for="city">City / Municipality</label>
                            <input id="city" name="city" type="text"
                                   class="profile-input @error('city') is-error @enderror"
                                   value="{{ old('city', $user->city) }}"
                                   placeholder="e.g. Quezon City">
                            @error('city')<span class="profile-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="profile-field">
                            <label class="profile-label" for="province">Province</label>
                            <input id="province" name="province" type="text"
                                   class="profile-input @error('province') is-error @enderror"
                                   value="{{ old('province', $user->province) }}"
                                   placeholder="e.g. Metro Manila">
                            @error('province')<span class="profile-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="profile-field">
                            <label class="profile-label" for="zip_code">ZIP Code</label>
                            <input id="zip_code" name="zip_code" type="text"
                                   class="profile-input @error('zip_code') is-error @enderror"
                                   value="{{ old('zip_code', $user->zip_code) }}"
                                   placeholder="e.g. 1100"
                                   maxlength="10">
                            @error('zip_code')<span class="profile-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── PAYMENT PREFERENCE ──────────────────────────── --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Preferred Payment Method</h3>
                    <p class="profile-section-hint">
                        This will be pre-selected at checkout.
                    </p>

                    <div class="payment-options">

                        <label class="payment-option {{ old('payment_method', $user->payment_method) === 'cod' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="cod"
                                   {{ old('payment_method', $user->payment_method) === 'cod' ? 'checked' : '' }}
                                   onchange="selectPayment(this)">
                            <div class="payment-option-icon">🚚</div>
                            <div class="payment-option-body">
                                <div class="payment-option-title">Cash on Delivery</div>
                                <div class="payment-option-desc">Pay in cash when your order arrives.</div>
                            </div>
                            <span class="payment-option-check">✓</span>
                        </label>

                        <label class="payment-option {{ old('payment_method', $user->payment_method) === 'gcash' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="gcash"
                                   {{ old('payment_method', $user->payment_method) === 'gcash' ? 'checked' : '' }}
                                   onchange="selectPayment(this)">
                            <div class="payment-option-icon">📱</div>
                            <div class="payment-option-body">
                                <div class="payment-option-title">GCash</div>
                                <div class="payment-option-desc">Pay via GCash e-wallet instantly.</div>
                            </div>
                            <span class="payment-option-check">✓</span>
                        </label>

                        <label class="payment-option {{ old('payment_method', $user->payment_method) === 'bank_transfer' ? 'selected' : '' }}">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                   {{ old('payment_method', $user->payment_method) === 'bank_transfer' ? 'checked' : '' }}
                                   onchange="selectPayment(this)">
                            <div class="payment-option-icon">🏦</div>
                            <div class="payment-option-body">
                                <div class="payment-option-title">Bank Transfer</div>
                                <div class="payment-option-desc">Transfer directly to our bank account.</div>
                            </div>
                            <span class="payment-option-check">✓</span>
                        </label>

                    </div>
                    @error('payment_method')
                        <span class="profile-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════
                 RIGHT SIDEBAR
            ══════════════════════════════════════════════════════ --}}
            <div class="profile-sidebar">

                {{-- Save button --}}
                <div class="admin-card profile-card">
                    <button type="submit" class="profile-save-btn">
                        Save Changes
                    </button>
                    <a href="{{ route('shop.index') }}" class="profile-cancel-link">
                        Cancel
                    </a>
                </div>

                {{-- Account summary --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Account Summary</h3>
                    <div class="profile-meta-list">
                        <div class="profile-meta-row">
                            <span class="profile-meta-label">Member since</span>
                            <span class="profile-meta-value">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="profile-meta-label">Total orders</span>
                            <span class="profile-meta-value">{{ auth()->user()->orders()->count() }}</span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="profile-meta-label">Role</span>
                            <span class="profile-meta-value">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Customer') }}</span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="profile-meta-label">Address</span>
                            <span class="profile-meta-value">
                                @if($user->hasCompleteAddress())
                                    <span style="color:#2e7d32;font-weight:700;">✓ Set</span>
                                @else
                                    <span style="color:#CC0008;">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="profile-meta-label">Payment</span>
                            <span class="profile-meta-value">
                                @if($user->payment_method)
                                    <span style="color:#2e7d32;font-weight:700;">✓ Set</span>
                                @else
                                    <span style="color:#CC0008;">Not set</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick links --}}
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Quick Links</h3>
                    <div class="profile-quick-links">
                        <a href="{{ route('orders.index') }}" class="profile-quick-link">
                            <span>📦</span> My Orders
                        </a>
                        <a href="{{ route('cart.index') }}" class="profile-quick-link">
                            <span>🛒</span> My Cart
                        </a>
                        <a href="{{ route('shop.index') }}" class="profile-quick-link">
                            <span>🛋️</span> Browse Shop
                        </a>
                    </div>
                </div>

            </div>

        </form>

        {{-- ── CHANGE PASSWORD (separate form) ─────────────────────── --}}
        <div class="profile-grid" style="margin-top:0;">
            <div class="profile-main">
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Change Password</h3>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="profile-field">
                            <label class="profile-label" for="current_password">Current Password</label>
                            <input id="current_password" name="current_password"
                                   type="password"
                                   class="profile-input @error('current_password', 'updatePassword') is-error @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <span class="profile-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="profile-two-col">
                            <div class="profile-field">
                                <label class="profile-label" for="password">New Password</label>
                                <input id="password" name="password"
                                       type="password"
                                       class="profile-input @error('password', 'updatePassword') is-error @enderror"
                                       autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <span class="profile-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="profile-field">
                                <label class="profile-label" for="password_confirmation">Confirm New Password</label>
                                <input id="password_confirmation" name="password_confirmation"
                                       type="password"
                                       class="profile-input"
                                       autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="profile-save-btn" style="max-width:200px;">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── DELETE ACCOUNT (sidebar) ─────────────────────── --}}
            <div class="profile-sidebar">
                <div class="admin-card profile-card" style="border:1.5px solid #ffcdd2;">
                    <h3 class="profile-section-title" style="color:#CC0008;">Danger Zone</h3>
                    <p style="font-size:var(--text-sm);color:var(--ikea-gray);margin-bottom:var(--space-sm);line-height:1.6;">
                        Once you delete your account, all your data will be permanently removed. This cannot be undone.
                    </p>
                    <button onclick="document.getElementById('deleteModal').style.display='flex'"
                            class="profile-delete-btn">
                        Delete My Account
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ── ADDRESS BOOK ──────────────────────────────── --}}
    <div class="admin-card profile-card">
        <h3 class="profile-section-title">Address Book</h3>

        @if(session('status') === 'address-added')
            <div class="profile-flash success">✅ Address added.</div>
        @endif
        @if(session('status') === 'address-updated')
            <div class="profile-flash success">✅ Address updated.</div>
        @endif
        @if(session('status') === 'address-deleted')
            <div class="profile-flash success">✅ Address removed.</div>
        @endif

        {{-- Existing addresses --}}
        @forelse($addresses as $addr)
            <div class="address-card {{ $addr->is_default ? 'address-card--default' : '' }}">
                <div class="address-card-header">
                    <span class="address-label">{{ $addr->label }}</span>
                    @if($addr->is_default)
                        <span class="address-badge-default">Default</span>
                    @endif
                </div>
                <div class="address-card-body">
                    <div class="address-name">{{ $addr->full_name }} · {{ $addr->phone }}</div>
                    <div class="address-text">{{ $addr->oneLiner() }}</div>
                </div>
                <div class="address-card-actions">
                    @if(!$addr->is_default)
                        <form method="POST" action="{{ route('addresses.default', $addr) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="address-btn-link">Set as default</button>
                        </form>
                    @endif
                    <button type="button" class="address-btn-link"
                            onclick="openEditAddress({{ $addr->id }}, '{{ addslashes($addr->label) }}', '{{ addslashes($addr->full_name) }}', '{{ addslashes($addr->phone) }}', '{{ addslashes($addr->address) }}', '{{ addslashes($addr->city) }}', '{{ addslashes($addr->province) }}', '{{ addslashes($addr->zip_code) }}', {{ $addr->is_default ? 'true' : 'false' }})">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('addresses.destroy', $addr) }}" style="display:inline;"
                        onsubmit="return confirm('Remove this address?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="address-btn-danger">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="font-size:13px;color:var(--ikea-gray);margin-bottom:var(--space-sm);">No saved addresses yet.</p>
        @endforelse

        {{-- Add new address button --}}
        <button type="button" class="admin-btn-secondary" style="margin-top:var(--space-sm);"
                onclick="document.getElementById('addAddressForm').style.display = document.getElementById('addAddressForm').style.display === 'none' ? 'block' : 'none'">
            + Add New Address
        </button>

        {{-- Add form (hidden by default) --}}
        <div id="addAddressForm" style="display:none;margin-top:var(--space-sm);">
            <form method="POST" action="{{ route('addresses.store') }}">
                @csrf
                @include('profile.partials.address-form')
                <button type="submit" class="admin-btn-primary" style="margin-top:var(--space-sm);">Save Address</button>
            </form>
        </div>
    </div>

    {{-- Edit address modal --}}
    <div id="editAddressModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
        <div style="background:var(--ikea-white);border-radius:12px;padding:28px;width:100%;max-width:480px;margin:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:700;margin:0;">Edit Address</h3>
                <button type="button" onclick="document.getElementById('editAddressModal').style.display='none'"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ikea-gray);">×</button>
            </div>
            <form method="POST" id="editAddressForm" action="">
                @csrf @method('PATCH')
                @include('profile.partials.address-form', ['editing' => true])
                <button type="submit" class="admin-btn-primary" style="margin-top:var(--space-sm);width:100%;">Update Address</button>
            </form>
        </div>
    </div>

    <script>
    function openEditAddress(id, label, fullName, phone, address, city, province, zipCode, isDefault) {
        const form = document.getElementById('editAddressForm');
        form.action = '/addresses/' + id;
        form.querySelector('[name="label"]').value      = label;
        form.querySelector('[name="full_name"]').value  = fullName;
        form.querySelector('[name="phone"]').value      = phone;
        form.querySelector('[name="address"]').value    = address;
        form.querySelector('[name="city"]').value       = city;
        form.querySelector('[name="province"]').value   = province;
        form.querySelector('[name="zip_code"]').value   = zipCode;
        form.querySelector('[name="is_default"]').checked = isDefault;
        document.getElementById('editAddressModal').style.display = 'flex';
    }
    </script>

    {{-- ── DELETE ACCOUNT MODAL ─────────────────────────────────────── --}}
    <div id="deleteModal" class="profile-modal-overlay" style="display:none;">
        <div class="profile-modal">
            <h3 class="profile-modal-title">Delete Account</h3>
            <p class="profile-modal-desc">
                Are you sure? This will permanently delete your account, order history, and all associated data.
                Enter your password to confirm.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="profile-field">
                    <label class="profile-label" for="del_password">Your Password</label>
                    <input id="del_password" name="password" type="password"
                           class="profile-input @error('password', 'userDeletion') is-error @enderror"
                           placeholder="Enter your password">
                    @error('password', 'userDeletion')
                        <span class="profile-error">{{ $message }}</span>
                    @enderror
                </div>
                <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
                    <button type="submit" class="profile-delete-btn" style="flex:1;">
                        Yes, delete my account
                    </button>
                    <button type="button"
                            onclick="document.getElementById('deleteModal').style.display='none'"
                            class="profile-cancel-link"
                            style="flex:1;text-align:center;padding:12px;border:1.5px solid var(--ikea-border);border-radius:40px;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function selectPayment(radio) {
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            radio.closest('.payment-option').classList.add('selected');
        }

        // Open delete modal if there are deletion errors
        @if($errors->userDeletion->isNotEmpty())
            document.getElementById('deleteModal').style.display = 'flex';
        @endif
        // Profile info form
    const profileForm = document.querySelector('form[action*="profile.update"]');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('.js-error').forEach(el => el.remove());

            const name  = this.querySelector('[name="name"]');
            const email = this.querySelector('[name="email"]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!name.value.trim()) {
                valid = false; showError(name, 'Full name is required.');
            } else if (name.value.trim().length < 2) {
                valid = false; showError(name, 'Name must be at least 2 characters.');
            }

            if (!email.value.trim()) {
                valid = false; showError(email, 'Email is required.');
            } else if (!emailRegex.test(email.value.trim())) {
                valid = false; showError(email, 'Please enter a valid email address.');
            }

            if (!valid) e.preventDefault();
        });
    }

    // Password change form
    const passwordForm = document.querySelector('form[action*="password.update"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('.js-error').forEach(el => el.remove());

            const current  = this.querySelector('[name="current_password"]');
            const password = this.querySelector('[name="password"]');
            const confirm  = this.querySelector('[name="password_confirmation"]');

            if (!current.value) {
                valid = false; showError(current, 'Current password is required.');
            }
            if (!password.value) {
                valid = false; showError(password, 'New password is required.');
            } else if (password.value.length < 8) {
                valid = false; showError(password, 'Password must be at least 8 characters.');
            }
            if (!confirm.value) {
                valid = false; showError(confirm, 'Please confirm your new password.');
            } else if (confirm.value !== password.value) {
                valid = false; showError(confirm, 'Passwords do not match.');
            }

            if (!valid) e.preventDefault();
        });
    }

    function showError(input, message) {
        input.style.borderColor = '#CC0008';
        const msg = document.createElement('p');
        msg.className = 'js-error';
        msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
        msg.textContent = message;
        input.parentNode.appendChild(msg);
    }

    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const err = this.parentNode.querySelector('.js-error');
            if (err) err.remove();
        });
    });
    </script>
    @endpush
</x-app-layout>