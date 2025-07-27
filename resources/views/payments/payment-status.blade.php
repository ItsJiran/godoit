@section('title', 'Payment Status')
<x-app-layout>
    <section class="hero payment-status">
        <div class="container minx-container">
            <!-- Success Status -->
            <div class="pmt-status-box pmt-status-success" id="successBox">
                <div class="pmt-status-header">
                    <h2 class="pmt-status-title">Payment Successful</h2>
                    <p class="pmt-status-subtitle">Transaction completed successfully</p>
                </div>
                <div class="pmt-status-content">
                    <div class="pmt-status-icon">
                        <div class="pmt-icon-circle">✓</div>
                    </div>
                    <h3 class="pmt-status-message">Payment Confirmed!</h3>
                    <p class="pmt-status-description">
                        Your payment has been processed successfully. You will receive a confirmation email shortly.
                    </p>
                    <div class="pmt-transaction-details">
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Transaction ID:</span>
                            <span class="pmt-detail-value">#TRX123456789</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Product:</span>
                            <span class="pmt-detail-value">Blanter AI Pro</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Date:</span>
                            <span class="pmt-detail-value">27 Jul 2025, 14:30</span>
                        </div>
                        <div class="pmt-detail-row">
                            <span class="pmt-detail-label">Amount:</span>
                            <span class="pmt-detail-value">Rp 10.000</span>
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