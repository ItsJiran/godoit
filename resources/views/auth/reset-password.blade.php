<x-guest-layout>
    <a href="{{ url('/login') }}" class="home-link">← Back to Login</a>
    
    <div class="auth-container page-reset-password">
        <div class="welcome-title">Reset Password</div>
        <div class="welcome-subtitle">Enter your new password below.</div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Email') }}</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-input" 
                       placeholder="Enter your email address" 
                       value="{{ $email ?? old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">{{ __('New Password') }}</label>
                <div class="password-field">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Enter your new password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">👁</button>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                <div class="password-field">
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-input" 
                           placeholder="Confirm your new password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">👁</button>
                </div>
                @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Reset Password') }}
            </button>
        </form>
    </div>
</x-guest-layout>