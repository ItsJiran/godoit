{{-- checkout.blade.php --}}

@section('title', 'Checkout')
@section('header')
    <link rel="stylesheet" href="{{ asset('css/demo/checkout.css') }}">
@endsection

<x-app-layout>

<section class="checkout-hero-section">
    <div class="checkout-container">
        <div class="payment-card">
            <div class="card-header">
                <h2>Order Details</h2>
            </div>
            <div class="card-body">
                <div class="order-summary">
                    <div class="order-item">
                        <span class="item-label">Product:</span>
                        <span class="item-value">Napak Tilas Kebangsaan</span>
                    </div>
                    <div class="order-item total-row">
                        @php
                        $harga = 10000;
                        @endphp
                        <span class="item-label">Total:</span>
                        <span class="item-value total-price" data-harga="{{$harga}}">Rp {{ number_format($harga, 0, '.', '.') }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('payments.create') }}" method="POST" class="checkout-form">
                @csrf

                {{-- User Information Fields --}}
                <h3 class="form-section-title">Your Information</h3>

                <div class="form-group">
                    <label for="nama" class="form-label">Name:</label>
                    <input type="text" id="nama" name="nama" class="form-input" value="{{ old('nama') }}" required>
                    @error('nama')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">Address:</label>
                    <textarea id="alamat" name="alamat" class="form-input" rows="3" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="umur" class="form-label">Age:</label>
                    <input type="number" id="umur" name="umur" class="form-input" value="{{ old('umur') }}" required>
                    @error('umur')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Hidden Inputs for Payment --}}
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="amount" value="{{ $harga }}"> {{-- Use the $harga variable for amount --}}

                <button type="submit" class="proceed-button">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</section>

</x-app-layout>