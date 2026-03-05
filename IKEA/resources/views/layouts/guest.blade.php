<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IKEA Philippines') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-body">

        <div class="auth-wrapper">

            {{-- Left panel — branding --}}
            <div class="auth-brand-panel" aria-hidden="true">
                <a href="/" class="auth-brand-logo">
                    <div class="logo-box">IKEA</div>
                </a>
                <div class="auth-brand-copy">
                    <h2>Beautiful homes<br>start <span>here.</span></h2>
                    <p>Join millions of members who enjoy exclusive deals, free design help, and early access to sales.</p>
                </div>

                {{-- CSS room illustration (reused from hero) --}}
                <div class="auth-room-scene" aria-hidden="true">
                    <div class="wall"></div>
                    <div class="floor"></div>
                    <div class="sofa">
                        <div class="sofa-back"></div>
                        <div class="sofa-seat">
                            <div class="sofa-leg left"></div>
                            <div class="sofa-leg right"></div>
                        </div>
                    </div>
                    <div class="plant">
                        <div class="plant-leaves"></div>
                        <div class="plant-pot"></div>
                    </div>
                    <div class="lamp">
                        <div class="lamp-shade"></div>
                        <div class="lamp-pole"></div>
                        <div class="lamp-base"></div>
                    </div>
                </div>
            </div>

            {{-- Right panel — form --}}
            <div class="auth-form-panel">
                <div class="auth-form-box">

                    {{-- Mobile logo (visible only on small screens) --}}
                    <a href="/" class="auth-mobile-logo">
                        <div class="logo-box">IKEA</div>
                    </a>

                    {{ $slot }}

                </div>

                <p class="auth-back-link">
                    <a href="/">← Back to homepage</a>
                </p>
            </div>

        </div>

    </body>
</html>