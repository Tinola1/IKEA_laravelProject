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

    <style>
        .profile-page {
            padding: var(--space-lg);
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: var(--space-md);
        }

        /* Flash */
        .profile-flash {
            padding: 12px var(--space-md);
            border-radius: 6px;
            font-size: var(--text-sm);
            font-weight: 600;
        }
        .profile-flash.success { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }

        /* Grid */
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: var(--space-md);
            align-items: start;
        }
        .profile-main    { display:flex; flex-direction:column; gap:var(--space-md); }
        .profile-sidebar { display:flex; flex-direction:column; gap:var(--space-md); }

        /* Card */
        .profile-card { padding: var(--space-md); }
        .profile-section-title {
            font-size: var(--text-base);
            font-weight: 800;
            color: var(--ikea-dark);
            margin-bottom: var(--space-sm);
            padding-bottom: var(--space-xs);
            border-bottom: 2px solid var(--ikea-border);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-section-hint {
            font-size: var(--text-sm);
            color: var(--ikea-gray);
            margin-bottom: var(--space-sm);
            margin-top: -8px;
        }
        .profile-badge-complete   { font-size:11px; font-weight:700; background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:40px; }
        .profile-badge-incomplete { font-size:11px; font-weight:700; background:#fff3e0; color:#f57c00; padding:2px 8px; border-radius:40px; }

        /* Avatar */
        .avatar-section { display:flex; gap:var(--space-md); align-items:center; }
        .avatar-preview-wrap { position:relative; flex-shrink:0; }
        .avatar-preview {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--ikea-border);
        }
        .avatar-edit-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28px;
            height: 28px;
            background: var(--ikea-yellow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            border: 2px solid white;
        }
        .avatar-name  { font-size:var(--text-lg); font-weight:800; color:var(--ikea-dark); }
        .avatar-email { font-size:var(--text-sm); color:var(--ikea-gray); margin-top:2px; }
        .avatar-role  { font-size:11px; font-weight:700; color:var(--ikea-blue); text-transform:uppercase; letter-spacing:.5px; margin-top:4px; }
        .avatar-upload-label {
            display: inline-block;
            margin-top: var(--space-xs);
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--ikea-blue);
            text-decoration: underline;
            cursor: pointer;
        }
        .avatar-input { display:none; }
        .avatar-hint  { font-size:11px; color:var(--ikea-gray); margin-top:4px; }

        /* Fields */
        .profile-field  { display:flex; flex-direction:column; gap:6px; margin-bottom:var(--space-sm); }
        .profile-field:last-child { margin-bottom:0; }
        .profile-label  { font-size:var(--text-sm); font-weight:700; color:var(--ikea-dark); }
        .profile-input  { width:100%; padding:10px 12px; border:1.5px solid var(--ikea-border); border-radius:6px; font-size:var(--text-base); font-family:'Noto Sans',sans-serif; color:var(--ikea-dark); background:white; transition:border-color var(--transition-fast); }
        .profile-input:focus { outline:none; border-color:var(--ikea-blue); box-shadow:0 0 0 3px rgba(0,88,163,.1); }
        .profile-input.is-error { border-color:#CC0008; }
        .profile-error  { font-size:var(--text-sm); color:#CC0008; font-weight:600; }
        .profile-unverified { font-size:var(--text-sm); color:#f57c00; font-weight:600; }

        .profile-two-col   { display:grid; grid-template-columns:1fr 1fr; gap:var(--space-sm); }
        .profile-three-col { display:grid; grid-template-columns:1fr 1fr 1fr; gap:var(--space-sm); }

        /* Payment options */
        .payment-options { display:flex; flex-direction:column; gap:var(--space-xs); }
        .payment-option {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: 14px var(--space-sm);
            border: 1.5px solid var(--ikea-border);
            border-radius: 8px;
            cursor: pointer;
            transition: all var(--transition-fast);
            position: relative;
        }
        .payment-option:hover { border-color: var(--ikea-blue); background: #f5f9ff; }
        .payment-option.selected { border-color: var(--ikea-blue); background: #e3f2fd; }
        .payment-option input[type="radio"] { display:none; }
        .payment-option-icon { font-size:24px; flex-shrink:0; }
        .payment-option-title { font-size:var(--text-base); font-weight:700; color:var(--ikea-dark); }
        .payment-option-desc  { font-size:var(--text-sm); color:var(--ikea-gray); }
        .payment-option-check {
            margin-left: auto;
            font-size: 16px;
            font-weight: 900;
            color: var(--ikea-blue);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }
        .payment-option.selected .payment-option-check { opacity:1; }

        /* Save button */
        .profile-save-btn {
            width: 100%;
            height: 48px;
            background: var(--ikea-yellow);
            color: var(--ikea-dark);
            border: none;
            border-radius: 40px;
            font-size: var(--text-base);
            font-weight: 700;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: background var(--transition-fast);
            margin-bottom: var(--space-xs);
        }
        .profile-save-btn:hover { background:#f0cc00; }
        .profile-cancel-link {
            display: block;
            text-align: center;
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--ikea-gray);
            text-decoration: none;
            padding: 8px;
        }
        .profile-cancel-link:hover { color:var(--ikea-dark); }

        /* Delete button */
        .profile-delete-btn {
            width: 100%;
            padding: 10px;
            background: #ffebee;
            color: #CC0008;
            border: 1.5px solid #ffcdd2;
            border-radius: 40px;
            font-size: var(--text-sm);
            font-weight: 700;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .profile-delete-btn:hover { background:#CC0008; color:white; border-color:#CC0008; }

        /* Meta list */
        .profile-meta-list { display:flex; flex-direction:column; gap:0; }
        .profile-meta-row  { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--ikea-border); font-size:var(--text-sm); }
        .profile-meta-row:last-child { border-bottom:none; }
        .profile-meta-label { color:var(--ikea-gray); font-weight:600; }
        .profile-meta-value { font-weight:700; color:var(--ikea-dark); }

        /* Quick links */
        .profile-quick-links { display:flex; flex-direction:column; gap:4px; }
        .profile-quick-link  { display:flex; align-items:center; gap:10px; padding:10px; border-radius:6px; color:var(--ikea-dark); text-decoration:none; font-size:var(--text-sm); font-weight:600; transition:background var(--transition-fast); }
        .profile-quick-link:hover { background:var(--ikea-light); }

        /* Delete modal */
        .profile-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .profile-modal {
            background: white;
            border-radius: 12px;
            padding: var(--space-xl);
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .profile-modal-title { font-size:var(--text-xl); font-weight:900; color:#CC0008; margin-bottom:var(--space-xs); }
        .profile-modal-desc  { font-size:var(--text-sm); color:var(--ikea-gray); line-height:1.6; margin-bottom:var(--space-md); }

        /* Responsive */
        @media (max-width:900px) {
            .profile-grid { grid-template-columns:1fr; }
            .profile-sidebar { order:-1; }
            .profile-two-col { grid-template-columns:1fr; }
            .profile-three-col { grid-template-columns:1fr 1fr; }
        }
        @media (max-width:560px) {
            .profile-page { padding:var(--space-md); }
            .profile-three-col { grid-template-columns:1fr; }
            .avatar-section { flex-direction:column; align-items:flex-start; }
        }
    </style>

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