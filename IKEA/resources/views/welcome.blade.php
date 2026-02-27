<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IKEA Philippines — Furniture & Home Furnishings</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --ikea-blue: #0058A3;
                --ikea-yellow: #FFDA1A;
                --ikea-dark: #111111;
                --ikea-light: #F5F5F0;
                --ikea-gray: #767676;
                --ikea-border: #DFDFDF;
                --ikea-white: #FFFFFF;
            }

            html { scroll-behavior: smooth; }

            body {
                font-family: 'Noto Sans', sans-serif;
                background: var(--ikea-white);
                color: var(--ikea-dark);
                min-height: 100vh;
                overflow-x: hidden;
            }

            /* ── TOP STRIP ── */
            .top-strip {
                background: var(--ikea-blue);
                color: white;
                text-align: center;
                font-size: 13px;
                font-weight: 500;
                padding: 8px 16px;
                letter-spacing: 0.02em;
            }

            /* ── HEADER ── */
            header {
                background: var(--ikea-yellow);
                padding: 0 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 72px;
                position: sticky;
                top: 0;
                z-index: 100;
                border-bottom: 2px solid rgba(0,0,0,0.08);
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 0;
                text-decoration: none;
            }

            .logo-box {
                background: var(--ikea-blue);
                color: var(--ikea-yellow);
                font-size: 28px;
                font-weight: 900;
                padding: 6px 14px;
                letter-spacing: -1px;
                border-radius: 4px;
                line-height: 1;
            }

            nav {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            nav a {
                text-decoration: none;
                color: var(--ikea-dark);
                font-size: 14px;
                font-weight: 600;
                padding: 8px 16px;
                border-radius: 4px;
                transition: background 0.15s;
                letter-spacing: 0.01em;
            }

            nav a:hover { background: rgba(0,0,0,0.08); }

            .btn-primary {
                background: var(--ikea-blue) !important;
                color: white !important;
                padding: 10px 20px !important;
                border-radius: 40px !important;
                font-weight: 700 !important;
                transition: background 0.15s, transform 0.1s !important;
            }

            .btn-primary:hover {
                background: #004f94 !important;
                transform: translateY(-1px);
            }

            /* ── HERO ── */
            .hero {
                background: var(--ikea-blue);
                color: white;
                display: grid;
                grid-template-columns: 1fr 1fr;
                min-height: 520px;
                overflow: hidden;
                position: relative;
            }

            .hero-content {
                padding: 80px 64px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 24px;
                z-index: 2;
            }

            .hero-tag {
                display: inline-block;
                background: var(--ikea-yellow);
                color: var(--ikea-dark);
                font-size: 12px;
                font-weight: 700;
                padding: 4px 12px;
                border-radius: 2px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                width: fit-content;
            }

            .hero h1 {
                font-size: clamp(36px, 4vw, 60px);
                font-weight: 900;
                line-height: 1.05;
                letter-spacing: -1.5px;
            }

            .hero h1 span {
                color: var(--ikea-yellow);
            }

            .hero p {
                font-size: 18px;
                line-height: 1.6;
                color: rgba(255,255,255,0.85);
                max-width: 420px;
            }

            .hero-cta {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .cta-main {
                background: var(--ikea-yellow);
                color: var(--ikea-dark);
                padding: 14px 32px;
                border-radius: 40px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                transition: transform 0.15s, box-shadow 0.15s;
                border: none;
                cursor: pointer;
            }

            .cta-main:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            }

            .cta-outline {
                background: transparent;
                color: white;
                padding: 14px 32px;
                border-radius: 40px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                border: 2px solid rgba(255,255,255,0.5);
                transition: border-color 0.15s;
            }

            .cta-outline:hover { border-color: white; }

            .hero-visual {
                position: relative;
                overflow: hidden;
                background: #003d73;
            }

            .hero-visual-inner {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Room illustration using CSS */
            .room-scene {
                width: 100%;
                height: 100%;
                position: relative;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                padding-bottom: 40px;
            }

            .floor {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 45%;
                background: linear-gradient(to bottom, #d4a96a, #b8894a);
                border-radius: 0;
            }

            .wall {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 58%;
                background: linear-gradient(to bottom, #e8e0d0, #d8cfc0);
            }

            .sofa {
                position: absolute;
                bottom: 38%;
                left: 50%;
                transform: translateX(-50%);
                width: 220px;
            }

            .sofa-back {
                background: #2c5f8a;
                height: 60px;
                border-radius: 8px 8px 0 0;
                position: relative;
            }

            .sofa-seat {
                background: #3a7ab5;
                height: 35px;
                border-radius: 0 0 6px 6px;
                margin-top: -2px;
            }

            .sofa-leg {
                position: absolute;
                bottom: -10px;
                width: 10px;
                height: 10px;
                background: #222;
                border-radius: 2px;
            }

            .sofa-leg.left { left: 20px; }
            .sofa-leg.right { right: 20px; }

            .plant {
                position: absolute;
                bottom: 43%;
                right: 18%;
            }

            .plant-pot {
                width: 24px;
                height: 22px;
                background: #c0805a;
                border-radius: 0 0 6px 6px;
                margin: auto;
            }

            .plant-leaves {
                width: 50px;
                height: 60px;
                background: radial-gradient(circle, #4a9b5c, #2d7a3e);
                border-radius: 50% 50% 30% 30%;
                margin-left: -13px;
                margin-bottom: -2px;
            }

            .table {
                position: absolute;
                bottom: 40%;
                left: 28%;
                width: 70px;
                height: 36px;
            }

            .table-top {
                background: #8B5E3C;
                height: 10px;
                border-radius: 3px;
            }

            .table-legs {
                display: flex;
                justify-content: space-between;
                padding: 0 6px;
            }

            .table-leg {
                width: 5px;
                height: 26px;
                background: #6d4a2e;
            }

            .lamp {
                position: absolute;
                top: 12%;
                right: 24%;
            }

            .lamp-shade {
                width: 40px;
                height: 30px;
                background: var(--ikea-yellow);
                clip-path: polygon(10% 0%, 90% 0%, 100% 100%, 0% 100%);
                opacity: 0.9;
            }

            .lamp-pole {
                width: 3px;
                height: 50px;
                background: #555;
                margin: 0 auto;
            }

            .lamp-base {
                width: 20px;
                height: 6px;
                background: #555;
                border-radius: 3px;
                margin: 0 auto;
            }

            .price-tag {
                position: absolute;
                top: 20px;
                right: 20px;
                background: var(--ikea-yellow);
                color: var(--ikea-dark);
                padding: 10px 14px;
                border-radius: 50%;
                width: 80px;
                height: 80px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-weight: 900;
                box-shadow: 0 4px 16px rgba(0,0,0,0.3);
                animation: float 3s ease-in-out infinite;
            }

            .price-tag .from { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; }
            .price-tag .amount { font-size: 22px; line-height: 1; }
            .price-tag .currency { font-size: 10px; }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-6px); }
            }

            /* ── CATEGORIES STRIP ── */
            .categories-strip {
                background: var(--ikea-light);
                padding: 32px;
                border-bottom: 1px solid var(--ikea-border);
            }

            .categories-strip h2 {
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: var(--ikea-gray);
                margin-bottom: 20px;
            }

            .categories-grid {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                padding-bottom: 4px;
                scrollbar-width: none;
            }

            .categories-grid::-webkit-scrollbar { display: none; }

            .category-chip {
                background: white;
                border: 1.5px solid var(--ikea-border);
                border-radius: 40px;
                padding: 10px 20px;
                font-size: 14px;
                font-weight: 600;
                white-space: nowrap;
                cursor: pointer;
                text-decoration: none;
                color: var(--ikea-dark);
                transition: all 0.15s;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .category-chip:hover, .category-chip.active {
                background: var(--ikea-blue);
                color: white;
                border-color: var(--ikea-blue);
            }

            .category-chip .icon { font-size: 18px; }

            /* ── FEATURES ── */
            .features {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                border-bottom: 1px solid var(--ikea-border);
            }

            .feature {
                padding: 40px 36px;
                border-right: 1px solid var(--ikea-border);
                display: flex;
                gap: 20px;
                align-items: flex-start;
                transition: background 0.15s;
            }

            .feature:last-child { border-right: none; }
            .feature:hover { background: var(--ikea-light); }

            .feature-icon {
                font-size: 36px;
                flex-shrink: 0;
                line-height: 1;
            }

            .feature h3 {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 6px;
            }

            .feature p {
                font-size: 14px;
                color: var(--ikea-gray);
                line-height: 1.5;
            }

            /* ── PROMO BANNERS ── */
            .promo-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 2px;
                background: var(--ikea-border);
            }

            .promo-card {
                padding: 56px 48px;
                position: relative;
                overflow: hidden;
                cursor: pointer;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                min-height: 360px;
                text-decoration: none;
                color: white;
            }

            .promo-card.blue { background: var(--ikea-blue); }
            .promo-card.dark { background: #1a1a1a; }
            .promo-card.yellow { background: var(--ikea-yellow); color: var(--ikea-dark); }

            .promo-bg-shape {
                position: absolute;
                top: -60px;
                right: -60px;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                opacity: 0.1;
                background: white;
                transition: transform 0.3s;
            }

            .promo-card:hover .promo-bg-shape { transform: scale(1.2); }

            .promo-card .label {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                opacity: 0.75;
                margin-bottom: 10px;
            }

            .promo-card h3 {
                font-size: clamp(24px, 3vw, 40px);
                font-weight: 900;
                line-height: 1.1;
                letter-spacing: -0.5px;
                margin-bottom: 16px;
            }

            .promo-card .link {
                font-size: 14px;
                font-weight: 700;
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .promo-stack {
                display: grid;
                grid-template-rows: 1fr 1fr;
                gap: 2px;
            }

            /* ── PRODUCT SHOWCASE ── */
            .section {
                padding: 64px 32px;
            }

            .section-header {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                margin-bottom: 32px;
            }

            .section-header h2 {
                font-size: 32px;
                font-weight: 900;
                letter-spacing: -0.5px;
            }

            .section-header a {
                color: var(--ikea-blue);
                font-size: 14px;
                font-weight: 700;
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .products-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 2px;
                background: var(--ikea-border);
            }

            .product-card {
                background: white;
                cursor: pointer;
                transition: transform 0.2s;
                text-decoration: none;
                color: var(--ikea-dark);
            }

            .product-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); z-index: 1; position: relative; }

            .product-img {
                aspect-ratio: 1;
                background: var(--ikea-light);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 72px;
                position: relative;
                overflow: hidden;
            }

            .product-badge {
                position: absolute;
                top: 12px;
                left: 12px;
                background: var(--ikea-blue);
                color: white;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 2px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .product-badge.new { background: #008641; }
            .product-badge.sale { background: #CC0008; }

            .product-info {
                padding: 16px;
                border-top: 1px solid var(--ikea-border);
            }

            .product-info .name {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 2px;
            }

            .product-info .desc {
                font-size: 13px;
                color: var(--ikea-gray);
                margin-bottom: 10px;
                line-height: 1.4;
            }

            .product-info .price {
                font-size: 20px;
                font-weight: 900;
                color: var(--ikea-dark);
            }

            .product-info .price-old {
                font-size: 13px;
                color: var(--ikea-gray);
                text-decoration: line-through;
                margin-left: 6px;
                font-weight: 400;
            }

            .add-btn {
                display: block;
                width: 100%;
                background: var(--ikea-yellow);
                border: none;
                padding: 10px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.15s;
                font-family: inherit;
                text-align: center;
            }

            .add-btn:hover { background: #f0cc00; }

            /* ── MEMBERSHIP ── */
            .membership {
                background: var(--ikea-blue);
                color: white;
                padding: 72px 64px;
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 40px;
                align-items: center;
            }

            .membership h2 {
                font-size: 40px;
                font-weight: 900;
                letter-spacing: -1px;
                line-height: 1.1;
                margin-bottom: 12px;
            }

            .membership h2 span { color: var(--ikea-yellow); }

            .membership p {
                font-size: 16px;
                color: rgba(255,255,255,0.8);
                max-width: 500px;
                line-height: 1.6;
            }

            .membership-perks {
                display: flex;
                gap: 32px;
                margin-top: 24px;
            }

            .perk {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                font-weight: 600;
            }

            .perk .check {
                width: 22px;
                height: 22px;
                background: var(--ikea-yellow);
                color: var(--ikea-dark);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 900;
                flex-shrink: 0;
            }

            .membership-actions {
                display: flex;
                flex-direction: column;
                gap: 12px;
                align-items: flex-end;
            }

            .btn-yellow {
                background: var(--ikea-yellow);
                color: var(--ikea-dark);
                padding: 14px 36px;
                border-radius: 40px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                white-space: nowrap;
                transition: transform 0.15s;
                display: block;
                text-align: center;
            }

            .btn-yellow:hover { transform: translateY(-2px); }

            .btn-ghost-white {
                color: white;
                font-size: 13px;
                font-weight: 600;
                text-decoration: underline;
                text-underline-offset: 3px;
                text-align: center;
            }

            /* ── FOOTER ── */
            footer {
                background: var(--ikea-dark);
                color: white;
                padding: 56px 32px 32px;
            }

            .footer-grid {
                display: grid;
                grid-template-columns: 2fr repeat(3, 1fr);
                gap: 40px;
                margin-bottom: 48px;
            }

            .footer-brand .logo-box {
                font-size: 22px;
                margin-bottom: 16px;
                display: inline-block;
            }

            .footer-brand p {
                font-size: 14px;
                color: rgba(255,255,255,0.6);
                line-height: 1.6;
                max-width: 280px;
            }

            .footer-col h4 {
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: rgba(255,255,255,0.5);
                margin-bottom: 16px;
            }

            .footer-col a {
                display: block;
                color: rgba(255,255,255,0.8);
                text-decoration: none;
                font-size: 14px;
                margin-bottom: 10px;
                transition: color 0.15s;
            }

            .footer-col a:hover { color: var(--ikea-yellow); }

            .footer-bottom {
                border-top: 1px solid rgba(255,255,255,0.1);
                padding-top: 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: rgba(255,255,255,0.4);
            }

            .footer-bottom a { color: rgba(255,255,255,0.4); text-decoration: none; }
            .footer-bottom a:hover { color: white; }

            /* ── RESPONSIVE ── */
            @media (max-width: 900px) {
                .hero { grid-template-columns: 1fr; }
                .hero-visual { display: none; }
                .features { grid-template-columns: 1fr; }
                .feature { border-right: none; border-bottom: 1px solid var(--ikea-border); }
                .products-grid { grid-template-columns: repeat(2, 1fr); }
                .promo-grid { grid-template-columns: 1fr; }
                .promo-stack { grid-template-rows: auto; grid-template-columns: 1fr 1fr; }
                .membership { grid-template-columns: 1fr; }
                .membership-actions { align-items: flex-start; }
                .footer-grid { grid-template-columns: 1fr 1fr; }
                header { padding: 0 16px; }
                .hero-content { padding: 48px 24px; }
            }
        </style>
    </head>
    <body>

        <!-- Top Strip -->
        <div class="top-strip">
            🚚 Free delivery on orders over ₱5,000 — Shop now and save!
        </div>

        <!-- Header -->
        <header>
            <a href="/" class="logo">
                <div class="logo-box">IKEA</div>
            </a>

            <nav>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                        <a href="{{ route('shop.index') }}">Shop</a>
                        <a href="{{ route('cart.index') }}">🛒 Cart</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Create account</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-content">
                <span class="hero-tag">New Collection 2025</span>
                <h1>Beautiful homes<br>start <span>here.</span></h1>
                <p>Discover furniture and home furnishings that make everyday life better — designed for real life at prices that make sense.</p>
                <div class="hero-cta">
                    <a href="{{ route('shop.index') }}" class="cta-main">Shop Now</a>
                    <a href="#" class="cta-outline">View Catalogue</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-visual-inner">
                    <div class="room-scene">
                        <div class="wall"></div>
                        <div class="floor"></div>
                        <div class="lamp">
                            <div class="lamp-shade"></div>
                            <div class="lamp-pole"></div>
                            <div class="lamp-base"></div>
                        </div>
                        <div class="plant">
                            <div class="plant-leaves"></div>
                            <div class="plant-pot"></div>
                        </div>
                        <div class="table">
                            <div class="table-top"></div>
                            <div class="table-legs">
                                <div class="table-leg"></div>
                                <div class="table-leg"></div>
                            </div>
                        </div>
                        <div class="sofa">
                            <div class="sofa-back"></div>
                            <div class="sofa-seat">
                                <div class="sofa-leg left"></div>
                                <div class="sofa-leg right"></div>
                            </div>
                        </div>
                        <div class="price-tag">
                            <span class="from">From</span>
                            <span class="amount">₱999</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Category Chips -->
        <div class="categories-strip">
            <h2>Shop by category</h2>
            <div class="categories-grid">
                <a href="{{ route('shop.index') }}" class="category-chip active"><span class="icon">🛋️</span> Sofas</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🛏️</span> Beds</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🪑</span> Chairs</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🪞</span> Storage</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🍽️</span> Dining</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🖥️</span> Desks</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">💡</span> Lighting</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🪴</span> Decoration</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🛁</span> Bathroom</a>
                <a href="{{ route('shop.index') }}" class="category-chip"><span class="icon">🍳</span> Kitchen</a>
            </div>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🚚</div>
                <div>
                    <h3>Free Delivery</h3>
                    <p>On all orders over ₱5,000. Fast and reliable delivery to your door.</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">🔄</div>
                <div>
                    <h3>365-Day Returns</h3>
                    <p>Not happy? Return it within a year for a full refund, no questions asked.</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">🏪</div>
                <div>
                    <h3>Showroom Experience</h3>
                    <p>Book an appointment and see our full range in person at our store.</p>
                </div>
            </div>
        </div>

        <!-- Promo Banners -->
        <div class="promo-grid">
            <a href="{{ route('shop.index') }}" class="promo-card blue">
                <div class="promo-bg-shape"></div>
                <div class="label">Limited Time Offer</div>
                <h3>Up to 30% off<br>Living Room<br>Essentials</h3>
                <span class="link">Shop the sale →</span>
            </a>
            <div class="promo-stack">
                <a href="{{ route('shop.index') }}" class="promo-card dark">
                    <div class="promo-bg-shape"></div>
                    <div class="label">New Arrivals</div>
                    <h3>Bedroom<br>Collection</h3>
                    <span class="link">Explore now →</span>
                </a>
                <a href="{{ route('shop.index') }}" class="promo-card yellow">
                    <div class="promo-bg-shape" style="background: #000; opacity: 0.05;"></div>
                    <div class="label" style="color: rgba(0,0,0,0.5);">Book Today</div>
                    <h3>Visit Our<br>Showroom</h3>
                    <span class="link">Book appointment →</span>
                </a>
            </div>
        </div>

        <!-- Popular Products -->
        <section class="section">
            <div class="section-header">
                <h2>Popular right now</h2>
                <a href="{{ route('shop.index') }}">See all products →</a>
            </div>
            <div class="products-grid">
                <a href="{{ route('shop.index') }}" class="product-card">
                    <div class="product-img">
                        🛋️
                        <span class="product-badge sale">Sale</span>
                    </div>
                    <div class="product-info">
                        <div class="name">EKTORP</div>
                        <div class="desc">3-seat sofa, Lofallet beige</div>
                        <div><span class="price">₱24,999</span><span class="price-old">₱32,999</span></div>
                    </div>
                    <div class="add-btn">Add to cart</div>
                </a>
                <a href="{{ route('shop.index') }}" class="product-card">
                    <div class="product-img">
                        🪑
                        <span class="product-badge new">New</span>
                    </div>
                    <div class="product-info">
                        <div class="name">POÄNG</div>
                        <div class="desc">Armchair, birch veneer/Knisa light beige</div>
                        <div><span class="price">₱8,499</span></div>
                    </div>
                    <div class="add-btn">Add to cart</div>
                </a>
                <a href="{{ route('shop.index') }}" class="product-card">
                    <div class="product-img">
                        🪞
                        <span class="product-badge">Popular</span>
                    </div>
                    <div class="product-info">
                        <div class="name">KALLAX</div>
                        <div class="desc">Shelf unit, white, 77x77 cm</div>
                        <div><span class="price">₱6,999</span></div>
                    </div>
                    <div class="add-btn">Add to cart</div>
                </a>
                <a href="{{ route('shop.index') }}" class="product-card">
                    <div class="product-img">
                        🛏️
                    </div>
                    <div class="product-info">
                        <div class="name">MALM</div>
                        <div class="desc">Bed frame, high, white/Luröy, 160x200 cm</div>
                        <div><span class="price">₱19,999</span><span class="price-old">₱24,999</span></div>
                    </div>
                    <div class="add-btn">Add to cart</div>
                </a>
            </div>
        </section>

        <!-- Membership -->
        <div class="membership">
            <div>
                <h2>Join <span>IKEA Family</span><br>— it's free.</h2>
                <p>Get exclusive member discounts, early access to sales, free design services, and more. Over 200 million members worldwide can't be wrong.</p>
                <div class="membership-perks">
                    <div class="perk">
                        <div class="check">✓</div>
                        <span>Member prices</span>
                    </div>
                    <div class="perk">
                        <div class="check">✓</div>
                        <span>Free design help</span>
                    </div>
                    <div class="perk">
                        <div class="check">✓</div>
                        <span>Early sale access</span>
                    </div>
                </div>
            </div>
            <div class="membership-actions">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-yellow">Create free account</a>
                @endif
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-yellow">Go to Dashboard</a>
                @endauth
                <span class="btn-ghost-white">Already a member? <a href="{{ route('login') }}" style="color: var(--ikea-yellow);">Log in</a></span>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo-box">IKEA</div>
                    <p>We create well-designed, functional home furnishings at prices so low that as many people as possible will be able to afford them.</p>
                </div>
                <div class="footer-col">
                    <h4>Shop</h4>
                    <a href="{{ route('shop.index') }}">All Products</a>
                    <a href="#">New Arrivals</a>
                    <a href="#">Sale</a>
                    <a href="#">Inspiration</a>
                </div>
                <div class="footer-col">
                    <h4>Customer Service</h4>
                    <a href="#">Track Your Order</a>
                    <a href="#">Returns & Refunds</a>
                    <a href="#">Assembly Help</a>
                    <a href="#">Book Appointment</a>
                </div>
                <div class="footer-col">
                    <h4>About</h4>
                    <a href="#">About IKEA</a>
                    <a href="#">Sustainability</a>
                    <a href="#">Careers</a>
                    <a href="#">Store Finder</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2025 IKEA Philippines. All rights reserved.</span>
                <div style="display:flex; gap: 20px;">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </footer>

    </body>
</html>