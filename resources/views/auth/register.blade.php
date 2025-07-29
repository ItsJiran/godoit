@section('title', 'Registrasi Akun - Godoit')
<x-guest-layout>
    <a href="{{ url('/') }}" class="home-link">← Home page</a>

    <div class="auth-container page-register">
        <div class="register-title">Create Account</div>
        <div class="register-subtitle">Join us today! Please fill in your information.</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">{{ __('Nama Lengkap') }}</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-input" 
                       placeholder="Enter your full name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="username">{{ __('Username') }}</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-input" 
                       placeholder="Enter your username" 
                       value="{{ old('username') }}" 
                       required 
                       autofocus 
                       autocomplete="username">
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="whatsapp">{{ __('Whatsapp') }}</label>
                <input type="text" 
                        id="whatsapp" 
                        name="whatsapp" 
                        class="form-input" 
                        placeholder="Enter your whatsapp" 
                        value="{{ old('whatsapp') }}" 
                        required 
                        autofocus 
                        autocomplete="whatsapp">
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="kota">{{ __('Kota/Provinsi') }}</label>
                <input type="text" 
                        id="kota" 
                        name="kota" 
                        class="form-input" 
                        placeholder="Enter your city" 
                        value="{{ old('kota') }}" 
                        required 
                        autofocus 
                        autocomplete="kota">
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Email') }}</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-input" 
                       placeholder="Enter your email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="username">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">{{ __('Password') }}</label>
                <div class="password-field">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Enter your password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"></path></svg></button>
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
                           placeholder="Confirm your password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"></path></svg></button>
                </div>
                @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="parent_referral_code">{{ __('Kode Refferal') }}</label>
                <input type="text" 
                       id="parent_referral_code" 
                       name="parent_referral_code" 
                       class="form-input" 
                       placeholder="Enter refferal code" 
                       value="{{ old('parent_referral_code', $parentReferralCode ?? '') }}" 
                       readonly
                       autofocus 
                       autocomplete="parent_referral_code">
                @error('parent_referral_code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Create Account') }}
            </button>

            <!--<div class="divider">
                <span>or</span>
            </div>

            <button type="button" class="btn-google">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" style="width: 16px; height: 16px;"><path fill="#4285f4" fill-opacity="1" fill-rule="evenodd" stroke="none" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"></path><path fill="#34a853" fill-opacity="1" fill-rule="evenodd" stroke="none" d="M9.003 18c2.43 0 4.467-.806 5.956-2.18l-2.909-2.26c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z"></path><path fill="#fbbc05" fill-opacity="1" fill-rule="evenodd" stroke="none" d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z"></path><path fill="#ea4335" fill-opacity="1" fill-rule="evenodd" stroke="none" d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.002 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z"></path></svg>
                Sign up with Google
            </button>-->

            <div class="auth-footer">
                Already have an account? 
                <a href="{{ route('login') }}">Sign in</a>
            </div>
        </form>
    </div>
</x-guest-layout>