@section('title', 'Lihat Produk')
<x-app-layout>
    <div class="container min-container">

    {{-- Flash message --}}
    @if(session('success'))
    <!-- Success -->
    <div class="alert alert-success">
        <div class="alert-icon">✓</div>
        <div class="alert-text">
            <div class="alert-title">Success!</div>
            <div class="alert-message">{{ session('success') }}</div>
        </div>
    </div>
    @endif
    @if ($errors->any())
    <!-- Error -->
    <div class="alert alert-error">
        <div class="alert-icon">✕</div>
        <div class="alert-text">
            <div class="alert-title">Error!</div>
            <div class="alert-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    @if(session('error'))
    <!-- Custom Error -->
    <div class="alert alert-error">
        <div class="alert-icon">✕</div>
        <div class="alert-text">
            <div class="alert-title">Error!</div>
            <div class="alert-message">{{ session('error') }}</div>
        </div>
    </div>
    @endif

        <div class="product-header">
            <h1>{{ $product->name ?? 'Premium Membership' }}</h1>
            <p>{{ $product->description ?? 'Unlock exclusive features and content with our premium offering.' }}</p>
        </div>

        <div class="product-details">
            <div class="detail-item">
                <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l3 3a1 1 0 001.414-1.414L11 9.586V6z" clip-rule="evenodd"></path></svg>
                <span class="detail-label">Type:</span>
                <span class="detail-value">{{ ucfirst($product->type ?? 'N/A') }}</span>
            </div>
            @if(isset($product->productable) && $product->productable instanceof \App\Models\Membership)
            <div class="detail-item">
                <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="detail-label">Access Duration:</span>
                <span class="detail-value">
                    @if($product->productable->duration_days)
                        {{ $product->productable->duration_days }} Days
                    @else
                        Lifetime
                    @endif
                </span>
            </div>
            @endif
            {{-- Add more detail items here based on your Product model's attributes --}}
        </div>

        <div class="product-price">
            <div class="price-label">Price:</div>
            <div class="price-value">
                <small>Rp</small> {{ number_format($product->price ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="call-to-action mb-3">
            @auth
                {{-- Check if the user already has this product --}}
                @if (Auth::user()->acquisitions()->where('product_id', $product->id)->active()->exists())
                    <button class="btn-buy" disabled>Already Acquired</button>
                    <p class="mt-3 text-gray-600">You already have an active acquisition for this product.</p>
                @else
                    {{-- Use a form for the purchase action --}}
                    <form action="{{ route('product.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn-buy">Buy Now</button>
                    </form>
                @endif
            @else
                <p class="text-gray-600 mb-3">Please log in to acquire this product.</p>
                <a href="{{ route('login') }}" class="btn-buy">Login to Buy</a>
            @endauth
        </div>
    </div>
</x-app-layout>