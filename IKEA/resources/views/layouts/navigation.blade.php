<header class="ikea-site-header" x-data="{ mobileOpen: false, profileOpen: false }">

    {{-- Logo --}}
    <a href="/" class="logo" aria-label="IKEA Philippines home">
        <div class="logo-box" aria-hidden="true">IKEA</div>
    </a>

    {{-- Desktop nav links --}}
    <nav class="ikea-nav-links" aria-label="Main navigation">

        <a href="{{ route('shop.index') }}"
           class="{{ request()->routeIs('shop.*') ? 'ikea-nav-active' : '' }}">
            Shop
        </a>

        @auth
            <a href="{{ route('cart.index') }}"
               class="{{ request()->routeIs('cart.*') ? 'ikea-nav-active' : '' }}"
               aria-label="Shopping cart">
                Cart
                @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
                @if($cartCount > 0)
                    <span class="ikea-cart-badge">{{ $cartCount }}</span>
                @endif
            </a>

            <a href="{{ route('orders.index') }}"
               class="{{ request()->routeIs('orders.*') ? 'ikea-nav-active' : '' }}">
                My Orders
            </a>

            {{-- Profile dropdown --}}
            <div class="ikea-nav-dropdown" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    @click.outside="open = false"
                    class="ikea-nav-dropdown-trigger"
                    :aria-expanded="open"
                    aria-haspopup="true"
                >
                    {{ Auth::user()->name }}
                    <svg class="ikea-nav-chevron" :class="{ 'rotate-180': open }"
                         width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="ikea-nav-dropdown-menu"
                    role="menu"
                >
                    <a href="{{ route('dashboard') }}" class="ikea-nav-dropdown-item" role="menuitem">
                        Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" class="ikea-nav-dropdown-item" role="menuitem">
                        Profile
                    </a>
                    <div class="ikea-nav-dropdown-divider"></div>

                    <button
                        type="button"
                        onclick="document.getElementById('logoutPopup').style.display='flex'"
                        class="ikea-nav-dropdown-item ikea-nav-dropdown-item--danger"
                        role="menuitem"
                    >
                        Log Out
                    </button>

                </div>
            </div>

        @else
            <a href="{{ route('login') }}">Log in</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">Create account</a>
            @endif
        @endauth
    </nav>

</header>

{{-- ── LOGOUT POPUP ─────────────────────────────────────────────── --}}
@auth
<div id="logoutPopup"
     onclick="if(event.target===this)this.style.display='none'"
     style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">

    <div class="logout-popup-box">

        {{-- Icon --}}
        <div class="logout-popup-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 17L21 12M21 12L16 7M21 12H9M9 3H7C5.89543 3 5 3.89543 5 5V19C5 20.1046 5.89543 21 7 21H9"
                      stroke="#FFDB00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        {{-- Text --}}
        <h3 class="logout-popup-title">Log out of IKEA?</h3>
        <p class="logout-popup-desc">
            You are about to log out of your IKEA Philippines account.
            Your cart and orders will still be saved when you log back in.
        </p>

        {{-- Buttons --}}
        <div class="logout-popup-actions">
            <button type="button"
                    onclick="document.getElementById('logoutPopup').style.display='none'"
                    class="logout-btn-cancel">
                Cancel
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn-confirm">
                    Log out
                </button>
            </form>
        </div>

    </div>
</div>

<style>
    .logout-popup-box {
        background: #2a2118;
        border-radius: 16px;
        padding: 36px 32px 28px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
        animation: logout-pop 0.2s ease;
        border: 1px solid rgba(255,255,255,0.08);
    }
    @keyframes logout-pop {
        from { transform: scale(0.92); opacity: 0; }
        to   { transform: scale(1);    opacity: 1; }
    }
    .logout-popup-icon {
        width: 72px;
        height: 72px;
        background: rgba(255,219,0,0.12);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border: 2px solid rgba(255,219,0,0.25);
    }
    .logout-popup-title {
        font-size: 20px;
        font-weight: 800;
        color: white;
        margin-bottom: 10px;
        font-family: 'Noto Sans', sans-serif;
    }
    .logout-popup-desc {
        font-size: 14px;
        color: rgba(255,255,255,0.55);
        line-height: 1.7;
        margin-bottom: 28px;
        font-family: 'Noto Sans', sans-serif;
    }
    .logout-popup-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .logout-btn-cancel {
        height: 40px;
        padding: 0 20px;
        background: transparent;
        color: rgba(255,255,255,0.6);
        border: 1.5px solid rgba(255,255,255,0.15);
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Noto Sans', sans-serif;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .logout-btn-cancel:hover {
        border-color: rgba(255,255,255,0.4);
        color: white;
    }
    .logout-btn-confirm {
        height: 40px;
        padding: 0 20px;
        background: rgba(255,219,0,0.15);
        color: #FFDB00;
        border: 1.5px solid rgba(255,219,0,0.4);
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Noto Sans', sans-serif;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .logout-btn-confirm:hover {
        background: #FFDB00;
        color: #111;
        border-color: #FFDB00;
    }
</style>
@endauth