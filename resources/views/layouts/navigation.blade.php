<header>
    <div class="container">
        <nav>
            <div class="logo">
                <a href="/" title="GoDoit">
                    <img src="{{asset('img/godoit.png')}}" alt="GoDoit"/>
                </a>
            </div>
            
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>

            <div class="auth-buttons">
                @guest
                    <div class="guest-buttons">
                        <a href="{{ route('login') }}" class="btn btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-register">Register</a>
                    </div>
                @endguest

                @auth
                    <div class="profile-dropdown">
                        <button class="profile-btn" onclick="toggleDropdown()">
                            {{ Auth::user()->name }} ▼
                        </button>
                        <div class="dropdown-content">
                            <a href="/dashboard">Dashboard</a>
                            <a href="{{ route('profile.edit') }}">My Profile</a>
                            <a href="{{ route('profile.edit') }}">Settings</a>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="logout-button dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </div>
</header>