<x-guest-layout>

    <div class="auth-form-header">
        <h1 class="auth-form-title">Create your account</h1>
        <p class="auth-form-subtitle">Join IKEA Family — it's free</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm" novalidate>
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
    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let valid = true;
        const errors = {};

        const name     = document.querySelector('[name="name"]');
        const email    = document.querySelector('[name="email"]');
        const password = document.querySelector('[name="password"]');
        const confirm  = document.querySelector('[name="password_confirmation"]');

        document.querySelectorAll('.js-error').forEach(el => el.remove());
        document.querySelectorAll('.auth-input').forEach(el => el.style.borderColor = '');

        if (!name.value.trim()) {
            errors.name = 'Full name is required.';
            valid = false;
        } else if (name.value.trim().length < 2) {
            errors.name = 'Name must be at least 2 characters.';
            valid = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            errors.email = 'Email address is required.';
            valid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            errors.email = 'Please enter a valid email address.';
            valid = false;
        }

        if (!password.value) {
            errors.password = 'Password is required.';
            valid = false;
        } else if (password.value.length < 8) {
            errors.password = 'Password must be at least 8 characters.';
            valid = false;
        }

        if (!confirm.value) {
            errors.password_confirmation = 'Please confirm your password.';
            valid = false;
        } else if (confirm.value !== password.value) {
            errors.password_confirmation = 'Passwords do not match.';
            valid = false;
        }

        Object.keys(errors).forEach(field => {
            const input = document.querySelector('[name="' + field + '"]');
            const msg = document.createElement('p');
            msg.className = 'auth-error js-error';
            msg.textContent = errors[field];
            input.parentNode.appendChild(msg);
            input.style.borderColor = '#CC0008';
        });

        if (!valid) e.preventDefault();
    });

    document.querySelectorAll('.auth-input').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            const err = this.parentNode.querySelector('.js-error');
            if (err) err.remove();
        });
    });
    </script>
</x-guest-layout>