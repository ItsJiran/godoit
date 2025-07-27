@section('title', 'Verifikasi Email - Godoit')
<x-guest-layout>
    <a href="{{ url('/login') }}" class="home-link">← Back to Login</a>
    
    <div class="auth-container page-verify-email">
        <div class="welcome-title">Verify Your Email</div>
        <div class="welcome-subtitle">We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.</div>

        @if (session('status') == 'verification-link-sent')
            <div class="status-message">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="verification-info">
            <div style="background: #f0f4ff; border: 2px solid #e0e7ff; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 15px;">📧</div>
                <p style="color: #4338ca; font-weight: 500; margin-bottom: 10px;">Check Your Email</p>
                <p style="color: #6b7280; font-size: 14px; line-height: 1.5;">
                    We sent a verification link to<br>
                    <strong>{{ auth()->user()->email }}</strong>
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="auth-footer" style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
            <form method="POST" action="{{ route('profile.update') }}" style="width: 100%;">
                @csrf
                @method('patch')
                <button type="button" class="btn-google" onclick="showEmailChangeForm()">
                    📝 Change Email Address
                </button>
            </form>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background: none; border: none; color: #6c63ff; text-decoration: underline; cursor: pointer; font-size: 14px;">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Email Change Modal (Hidden by default) -->
    <div id="emailChangeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 20px; padding: 30px; max-width: 400px; width: 90%; margin: 20px;">
            <h3 style="margin-bottom: 20px; color: #2d3748; font-weight: 600;">Change Email Address</h3>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')
                <div class="form-group">
                    <label class="form-label" for="new_email">New Email Address</label>
                    <input type="email" 
                           id="new_email" 
                           name="email" 
                           class="form-input" 
                           placeholder="Enter new email address" 
                           required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">Update Email</button>
                    <button type="button" onclick="hideEmailChangeForm()" style="flex: 1; background: #e2e8f0; color: #4a5568; border: none; border-radius: 12px; padding: 16px; font-weight: 600; cursor: pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showEmailChangeForm() {
            document.getElementById('emailChangeModal').style.display = 'flex';
        }
        
        function hideEmailChangeForm() {
            document.getElementById('emailChangeModal').style.display = 'none';
        }
    </script>
</x-guest-layout>