<header>
    <div class="container">
        <nav>
            <div class="logo">
                <a href="/" title="Godoit">
                    <img src="{{asset('img/godoit.png')}}" alt="Godoit"/>
                </a>
            </div>
            
            <ul class="nav-links">
                @auth
                @if(Auth::user()->role == "user")
                <li><a href="/memberarea" title="Member Area">Member Area</a></li>
                <li><a href="/transaction" title="Transaction">Transaction</a></li>
                <li><a href="/marketing-kit" title="Marketing Kit">Marketing Kit</a></li>
                <li><a href="/contact" title="Contact Us">Contact Us</a></li>
                @else
                <li><a href="/dashboard" title="Dashboard">Dashboard</a></li>
                <li><a href="/admin/product" title="Products">Products</a></li>
                <li><a href="/admin/transaction" title="Transaction">Transaction</a></li>
                <li><a href="/admin/marketing-kit" title="Marketing Kit">Marketing Kit</a></li>
                @endif
                @endauth
                @guest
                <li><a href="/" title="Home">Home</a></li>
                <li><a href="/products" title="Products">Products</a></li>
                <li><a href="/about" title="About Us">About Us</a></li>
                <li><a href="/contact" title="Contact Us">Contact Us</a></li>
                @endguest
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
                            <a href="{{ route('profile.edit') }}" title="My Profile">My Profile</a>
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