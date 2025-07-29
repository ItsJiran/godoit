@section('title', 'Status Pembayaran')
<x-app-layout>
    <section class="hero payment-status">
        <div class="container min-container">
            @php
                // Determine status classes and content
                $statusClass = '';
                $statusIcon = '';
                $statusTitle = '';
                $statusMessage = '';
                $statusDescription = '';
                switch($payment->status) {
                    case 0: // Pending
                        $statusClass = 'pmt-status-pending';
                        $statusIcon = '<div class="pmt-loading-animation"></div>';
                        $statusTitle = 'Payment Pending';
                        $statusMessage = 'Processing Payment...';
                        $statusDescription = 'Your payment is being processed. Please wait while we confirm your transaction.';
                        break;
                    case 1: // Success
                        $statusClass = 'pmt-status-success';
                        $statusIcon = '✓';
                        $statusTitle = 'Pembayaran Berhasil';
                        $statusMessage = 'Payment Confirmed!';
                        $statusDescription = 'Thank you. We will contact you later.';
                        break;
                    case 2: // Failed
                        $statusClass = 'pmt-status-failed';
                        $statusIcon = '✕';
                        $statusTitle = 'Payment Failed';
                        $statusMessage = 'Payment Failed';
                        $statusDescription = 'Unfortunately, your payment could not be processed. Please try again or use a different payment method.';
                        break;
                }
            @endphp

            <div class="pmt-status-box {{ $statusClass }}">
                <div class="pmt-status-header">
                    <h2 class="pmt-status-title">{{ $statusTitle }}</h2>
                </div>
                <div class="pmt-status-content">
                    <div class="pmt-status-icon">
                        <div class="pmt-icon-circle">
                            {!! $statusIcon !!}
                        </div>
                    </div>
                    <h3 class="pmt-status-message">{{ $statusMessage }}</h3>
                    <p class="pmt-status-description">
                        {{ $statusDescription }}
                    </p>
                    
                    {{-- Transaction Details (reusable for all statuses) --}}
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
                        
                        {{-- Product Details Section --}}
                        @if(isset($payment->product_details) && is_array($payment->product_details))
                            <div class="pmt-border" style="border-top: 1px solid #e2e8f0; margin: 12px 0;"></div>
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
                            <div class="pmt-border" style="border-top: 1px solid #e2e8f0; margin: 12px 0;"></div>
                            <div class="pmt-detail-row">
                                <span class="pmt-detail-label">Total:</span>
                                <span class="pmt-detail-value">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Action Buttons based on status --}}
                    @if($payment->status == 1) {{-- Success --}}
                        <div class="pmt-action-buttons">
                            <a href="/transaction" class="pmt-btn pmt-btn-primary" title="Transaction">Transaction</a>
                            <a href="/" class="pmt-btn pmt-btn-secondary" title="Back to Home">Back To Home</a>
                        </div>
                    @elseif($payment->status == 0) {{-- Pending --}}
                        <div class="pmt-action-buttons">
                            <a href="/payments/{{$payment->id}}" class="pmt-btn pmt-btn-primary">Pembayaran</a>
                        </div>
                    @elseif($payment->status == 2) {{-- Failed --}}
                        <div class="pmt-action-buttons">
                            <a href="/" class="pmt-btn pmt-btn-primary">Back to Home</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>