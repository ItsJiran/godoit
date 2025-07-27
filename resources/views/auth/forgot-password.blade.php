@section('title', 'Forgot Password - Godoit')
<x-guest-layout>
    <a href="{{ url('/login') }}" class="home-link">← Back to Login</a>
    
    <div class="auth-container page-forgot-password">
        <div class="welcome-title">Forgot Password?</div>
        <div class="welcome-subtitle">No worries! Enter your email and we'll send you a reset link.</div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="status-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Email') }}</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-input" 
                       placeholder="Enter your email address" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Send Reset Link') }}
            </button>
        </form>
    </div>
</x-guest-layout>