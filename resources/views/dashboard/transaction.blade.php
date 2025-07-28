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
        @php
            // Decode jika masih string
            $productDetails = is_string($payment->product_details) ? json_decode($payment->product_details, true) : $payment->product_details;
            $customerDetails = is_string($payment->customer_details) ? json_decode($payment->customer_details, true) : $payment->customer_details;

            // Hitung total biaya
            $totalBiaya = 0;
            if (is_array($productDetails)) {
                foreach ($productDetails as $item) {
                    $totalBiaya += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                }
            }
        @endphp
            <div class="unit-list-item">
                <!-- Info Dasar -->
                <div class="unit-item-row">
                    <div class="unit-item-label">Order ID:</div>
                    <div class="unit-item-value">
                        <b>#{{ $payment->id_order }}</b>
                    </div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Nama:</div>
                    <div class="unit-item-value">{{ $customerDetails['first_name'] ?? '-' }}</div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Tanggal:</div>
                    <div class="unit-item-value">{{ $payment->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Total Biaya:</div>
                    <div class="unit-item-value"><b>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</b></div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Status:</div>
                    <div class="unit-item-value">
                        @if($payment->status == 0) Pending @endif
                        @if($payment->status == 1) Selesai @endif
                        @if($payment->status == 2) Gagal @endif
                    </div>
                </div>
                <div class="unit-action-buttons mobile-action-buttons">
                <a href="{{ route('payments.show', $payment->id) }}" class="unit-btn unit-btn-edit">Payment</a>
                <a href="{{ route('payment.status', $payment->id) }}" class="unit-btn unit-btn-edit">Bill Status</a>
            </div>
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