<x-guest-layout>
    <a href="{{ url('/login') }}" class="home-link">← Back to Login</a>

    <div class="auth-container page-confirm-password">
        <div class="welcome-title">Confirm Password</div>
        <div class="welcome-subtitle">For your security, please confirm your password to continue.</div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">{{ __('Password') }}</label>
                <div class="password-field">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Enter your current password" 
                           required 
                           autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">👁</button>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Confirm') }}
            </button>

            <div class="auth-footer">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sign out instead
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </form>
    </div>
</x-guest-layout>