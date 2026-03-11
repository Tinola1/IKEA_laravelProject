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

            @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('admin.orders.index') }}"
                   class="{{ request()->routeIs('admin.orders.*') ? 'ikea-nav-active' : '' }}">
                    Admin Orders
                </a>
                <a href="{{ route('admin.inventory.index') }}"
                   class="{{ request()->routeIs('admin.inventory.*') ? 'ikea-nav-active' : '' }}">
                    Inventory
                </a>
            @endif

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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ikea-nav-dropdown-item ikea-nav-dropdown-item--danger" role="menuitem">
                            Log Out
                        </button>
                    </form>
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