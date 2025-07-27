@section('title', 'Payment Status')
<x-app-layout>
    <section class="hero payment-status">
        <div class="container minx-container">
            <!-- Success Status -->
            <div class="pmt-status-box pmt-status-success" id="successBox">
                <div class="pmt-status-header">
                    <h2 class="pmt-status-title">Payment Successful</h2>
                </div>
                <div class="pmt-status-content">
                    <div class="pmt-status-icon">
                        <div class="pmt-icon-circle">✓</div>
                    </div>
                    <h3 class="pmt-status-message">Payment Confirmed!</h3>
                    <p class="pmt-status-description">
                        Your payment has been processed successfully.
                    </p>
                    <div class="pmt-transaction-details">
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Transaction ID:</span>
                            <span class="pmt-detail-value">#{{ $payment->transaction_details['order_id'] ?? '-' }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Customer Name:</span>
                            <span class="pmt-detail-value">{{ $payment->customer_details['first_name'] ?? '-' }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Email:</span>
                            <span class="pmt-detail-value">{{ $payment->customer_details['email'] ?? '-' }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Phone:</span>
                            <span class="pmt-detail-value">{{ $payment->customer_details['phone'] ?? '-' }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Payment Date:</span>
                            <span class="pmt-detail-value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="pmt-border"></div>
                        @php $total = 0; @endphp
                        @foreach ($payment->product_details as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        @if($item['id'] != "SERVICE_FEE")
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">ID Product:</span>
                            <span class="pmt-detail-value">{{ $item['id'] }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Product:</span>
                            <span class="pmt-detail-value">{{ $item['name'] }}</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Price:</span>
                            <span class="pmt-detail-value">Rp{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</span>
                        </div>
                        @else
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Service Fee:</span>
                            <span class="pmt-detail-value">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @endforeach
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Total:</span>
                            <span class="pmt-detail-value">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="pmt-action-buttons">
                        <button class="pmt-btn pmt-btn-primary">Continue</button>
                        <button class="pmt-btn pmt-btn-secondary">Download Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>