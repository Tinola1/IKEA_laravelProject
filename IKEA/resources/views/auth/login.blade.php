<x-guest-layout>

    <div class="auth-form-header">
        <h1 class="auth-form-title">Welcome back</h1>
        <p class="auth-form-subtitle">Log in to your IKEA Philippines account</p>
    </div>

    {{-- Session status (e.g. password reset success) --}}
    <x-auth-session-status class="auth-session-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        {{-- Email --}}
        <div class="auth-field">
            <x-input-label for="email" :value="__('Email address')" class="auth-label" />
            <x-text-input
                id="email"
                class="auth-input"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
            />
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <div class="auth-label-row">
                <x-input-label for="password" :value="__('Password')" class="auth-label" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-forgot-link">
                        Forgot password?
                    </a>
                @endif
            </div>
            <x-text-input
                id="password"
                class="auth-input"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        {{-- Remember me --}}
        <div class="auth-remember">
            <label for="remember_me" class="auth-checkbox-label">
                <input id="remember_me" type="checkbox" class="auth-checkbox" name="remember">
                <span>Remember me</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn">
            Log in
        </button>

        {{-- Register link --}}
        <p class="auth-switch-link">
            Don't have an account?
            <a href="{{ route('register') }}">Create one free</a>
        </p>

    </form>

</x-guest-layout>