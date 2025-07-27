@section('title', 'Pembayaran')
<x-app-layout>
    <section class="hero payment-box">
        <div class="container min-container">
            <!-- Payment Box -->
            <div class="payment-card">
                <div class="payment-content">
                    <div class="checkout-form">
                        <div class="order-summary">
                            <div class="order-item">
                                <span class="item-label">Product:</span>
                                <span class="item-value">Napak Tilas Kebangsaan</span>
                            </div>
                            <div class="order-item">
                                @php
                                $harga = 10000;
                                @endphp
                                <span class="item-label">Total:</span>
                                <span class="item-value total-price" data-harga="{{$harga}}">Rp {{ number_format($harga, 0, '.', '.') }}</span>
                            </div>
                        </div>
                        <div class="payment-actions">
                            <button class="pay-btn" id="pay-button">Pay Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
    <!--<script type="text/javascript"
            src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>-->
    <!-- PAYMENT -->
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $payment->snap_token }}', {
                onSuccess: function(result){
                    // Kirim status 'success' ke server
                    $.post("{{ route('transaction.pay') }}", {
                        _method: 'POST',
                        _token: '{{ csrf_token() }}',
                        status: 'success',
                        order_id: result.order_id,
                    }, function(data){
                        console.log('Status updated: ', data);
                        window.location.href = '/payment/status/{{ $payment->id }}';
                    });
                },
                onPending: function(result){
                    // Kirim status 'pending' ke server
                    $.post("{{ route('transaction.pay') }}", {
                        _method: 'POST',
                        _token: '{{ csrf_token() }}',
                        status: 'pending',
                        order_id: result.order_id,
                    }, function(data){
                        console.log('Status updated: ', data);
                        window.location.href = '/payment/status/{{ $payment->id }}';
                    });
                },
                onError: function(result){
                    // Kirim status 'error' ke server
                    $.post("{{ route('transaction.pay') }}", {
                        _method: 'POST',
                        _token: '{{ csrf_token() }}',
                        status: 'error',
                        order_id: result.order_id,
                    }, function(data){
                        console.log('Status updated: ', data);
                        window.location.href = '/payment/status/{{ $payment->id }}';
                    });
                },
                onClose: function(){
                    alert('Kamu menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        });
    </script>
</x-app-layout>