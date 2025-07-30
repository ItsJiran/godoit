@extends('layouts.admin')
@section('title', 'Transaction')
@section('content')
<div class="container">
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
    <div class="unit-list-wrapper unit-tablemodel custom-table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
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
                <tr>
                    <td data-label="Order ID"><b>#{{ $payment->id_order }}</b></td>
                    <td data-label="Nama">{{ $customerDetails['first_name'] ?? '-' }}</td>
                    <td data-label="Tanggal">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                    <td data-label="Total Biaya"><b>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                    <td data-label="Status">
                        @if($payment->status == 0)
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($payment->status == 1)
                            <span class="status-badge status-success">Selesai</span>
                        @elseif($payment->status == 2)
                            <span class="status-badge status-rejected">Gagal</span>
                        @endif
                    </td>
                    <td data-label="Action">
                        <div class="unit-action-buttons mobile-action-buttons unit-fullrow">
                            @if($payment->status == 0)
                            <a href="{{ route('payments.show', $payment->id) }}" class="unit-btn unit-btn-edit">Payment</a>
                            @endif
                            <a href="{{ route('payment.status', $payment->id) }}" class="unit-btn unit-btn-edit">Bill Status</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="unit-empty-state">
                    <td colspan="6">
                        Belum ada data pembayaran.
                        <br>
                        <small>Coba lakukan pencarian atau tunggu transaksi baru.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <!-- PAGINATION -->
    <x-pagination :paginator="$payments" />
</div>
@endsection