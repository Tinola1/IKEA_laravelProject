<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? '') ? $title . ' — ' . config('app.name', 'IKEA Philippines') : 'Admin — ' . config('app.name', 'IKEA Philippines') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-layout font-sans antialiased">

        {{-- Sidebar --}}
        @include('admin._sidebar')

        {{-- Mobile topbar (hamburger visible only on small screens) --}}
        <div class="admin-topbar">
            <button class="admin-topbar-toggle" onclick="toggleSidebar()" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="admin-topbar-logo">
                <div class="logo-box" style="font-size:15px;padding:3px 8px;">IKEA</div>
                <span>Admin</span>
            </a>
        </div>

        {{-- Page sub-header --}}
        @isset($header)
            <div class="ikea-page-subheader">
                <div class="ikea-page-subheader-inner">
                    {{ $header }}
                </div>
            </div>
        @endisset

        {{-- Page Content --}}
        <main>
            {{ $slot }}
        </main>

        @stack('scripts')

    </body>
</html>

<style>
    /* Mobile topbar */
    .admin-topbar {
        display: none;
        align-items: center;
        gap: 12px;
        background: #0a0a0a;
        padding: 12px 16px;
        position: sticky;
        top: 0;
        z-index: 50;
    }
    .admin-topbar-toggle {
        display: flex;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
    }
    .admin-topbar-toggle span {
        display: block;
        width: 22px;
        height: 2px;
        background: white;
        border-radius: 2px;
    }
    .admin-topbar-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .admin-topbar-logo span {
        color: rgba(255,255,255,0.6);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    @media (max-width: 900px) {
        .admin-topbar { display: flex; }
    }
</style>