@extends('layouts.admin')
@section('title', 'Transaction')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Transaction</h1>
    </div>

    <!-- Search -->
    <div class="search-form">
        <form action="{{ route('admin.transaction') }}" method="GET">
            <input type="text" name="search" placeholder="Cari Transaksi..." required="" value="{{ $query ?? '' }}">
            <button type="submit">Cari</button>
        </form>
        @if ($query)
            <a href="{{ route('admin.transaction') }}">Reset Pencarian</a>
        @endif
    </div>

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

    <!-- START -->
    <div class="unit-list-wrapper">
        @forelse($payments as $payment)
        <div class="unit-list-item">
            <!-- Basic Info -->
            <div class="unit-item-row">
                <div class="unit-item-label">Nama:</div>
                <div class="unit-item-value"><b>{{ $payment->name }}</b>
                </div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Email:</div>
                <div class="unit-item-value">{{ $payment->email }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Whatsapp:</div>
                <div class="unit-item-value">{{ $payment->whatsapp }}</div>
            </div>

            <!-- Product Details -->
            @if(!empty($payment->product_details))
                <div class="unit-item-row">
                    <div class="unit-item-label">Produk:</div>
                    <div class="unit-item-value">
                        @foreach($payment->product_details as $item)
                            <div>{{ $item['name'] ?? '-' }} x{{ $item['quantity'] ?? 1 }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Transaction Details -->
            @if(!empty($payment->transaction_details))
                <div class="unit-item-row">
                    <div class="unit-item-label">Total Bayar:</div>
                    <div class="unit-item-value">
                        Rp {{ number_format($payment->transaction_details['gross_amount'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Status:</div>
                    <div class="unit-item-value">{{ ucfirst($payment->transaction_details['transaction_status'] ?? '-') }}</div>
                </div>
            @endif

            <!-- Customer Details -->
            @if(!empty($payment->customer_details))
                <div class="unit-item-row">
                    <div class="unit-item-label">Alamat:</div>
                    <div class="unit-item-value">
                        {{ $payment->customer_details['address'] ?? '-' }},
                        {{ $payment->customer_details['city'] ?? '' }},
                        {{ $payment->customer_details['postal_code'] ?? '' }}
                    </div>
                </div>
            @endif

            <hr>
        </div>
    @empty
        <div class="unit-empty-state">
            Belum ada data pembayaran.
            <br>
            <small>Coba lakukan pencarian atau tunggu transaksi baru.</small>
        </div>
    @endforelse
    </div>

    <!-- PAGINATION -->
    <x-pagination :paginator="$payments" />
</div>
@endsection