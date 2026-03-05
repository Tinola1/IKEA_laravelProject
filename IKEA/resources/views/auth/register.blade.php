<x-guest-layout>

    <div class="auth-form-header">
        <h1 class="auth-form-title">Create your account</h1>
        <p class="auth-form-subtitle">Join IKEA Family — it's free</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        {{-- Name --}}
        <div class="auth-field">
            <x-input-label for="name" :value="__('Full name')" class="auth-label" />
            <x-text-input
                id="name"
                class="auth-input"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Juan dela Cruz"
            />
            <x-input-error :messages="$errors->get('name')" class="auth-error" />
        </div>

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
                autocomplete="username"
                placeholder="you@example.com"
            />
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" class="auth-label" />
            <x-text-input
                id="password"
                class="auth-input"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Min. 8 characters"
            />
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        {{-- Confirm Password --}}
        <div class="auth-field">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" class="auth-label" />
            <x-text-input
                id="password_confirmation"
                class="auth-input"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Re-enter your password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" />
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn">
            Create free account
        </button>

        {{-- Login link --}}
        <p class="auth-switch-link">
            Already have an account?
            <a href="{{ route('login') }}">Log in</a>
        </p>

    </form>

</x-guest-layout>