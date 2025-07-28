@section('title', 'Lihat Produk')
<x-app-layout>

    <style>
        /* General container styling */
        .product-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            font-family: 'Inter', sans-serif;
            color: #333;
        }
    
        /* Product header */
        .product-header {
            text-align: center;
            margin-bottom: 30px;
        }
    
        .product-header h1 {
            font-size: 2.8rem;
            color: #34495e;
            margin-bottom: 10px;
            font-weight: 700;
        }
    
        .product-header p {
            font-size: 1.1rem;
            color: #7f8c8d;
            line-height: 1.6;
        }
    
        /* Product details grid */
        .product-details {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 40px;
        }
    
        .detail-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #ecf0f1;
        }
    
        .detail-item:last-child {
            border-bottom: none;
        }
    
        .detail-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            color: #2c3e50;
        }
    
        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 1rem;
            min-width: 120px; /* Ensure labels align */
        }
    
        .detail-value {
            font-size: 1rem;
            color: #333;
            flex-grow: 1;
        }
    
        /* Price section */
        .product-price {
            text-align: center;
            margin-bottom: 40px;
        }
    
        .product-price .price-label {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
    
        .product-price .price-value {
            font-size: 3.5rem;
            font-weight: 800;
            color: #27ae60; /* Green for price */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
    
        .price-value small {
            font-size: 1.5rem;
            font-weight: 600;
            color: #27ae60;
        }
    
        /* Call to action button */
        .call-to-action {
            text-align: center;
        }
    
        .btn-buy {
            background-color: #3498db; /* Blue for action */
            color: #ffffff;
            padding: 15px 40px;
            font-size: 1.3rem;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
    
        .btn-buy:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
    
        .btn-buy:active {
            transform: translateY(0);
        }
    
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-container {
                margin: 30px 15px;
                padding: 20px;
            }
    
            .product-header h1 {
                font-size: 2rem;
            }
    
            .product-header p {
                font-size: 1rem;
            }
    
            .product-price .price-value {
                font-size: 2.8rem;
            }
    
            .price-value small {
                font-size: 1.2rem;
            }
    
            .btn-buy {
                padding: 12px 30px;
                font-size: 1.1rem;
            }
        }
    
        @media (max-width: 480px) {
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>

<div class="product-container">

    @if (session('error'))
    <div class="status-message">
        {{ session('error') }}
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

    <div class="call-to-action">
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