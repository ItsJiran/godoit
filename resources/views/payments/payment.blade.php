@section('title', 'Pembayaran')
<x-app-layout>
    <section class="hero payment-box">
        <div class="container min-container">
            <!-- Payment Box -->
            <div class="payment-card">
                <div class="payment-content">
                    <div class="checkout-form">
                        <div class="order-summary">
                            @php $total = 0; @endphp
                            @foreach ($payment->product_details as $item)
                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            @endphp
                            @if($item['id'] != "SERVICE_FEE")
                            <div class="order-item">
                                <span class="item-label">Product:</span>
                                <span class="item-value">{{ $item['name'] }}</span>
                            </div>
                            <div class="order-item">
                                <span class="item-label">Price:</span>
                                <span class="item-value">Rp{{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</span>
                            </div>
                            @else
                            <div class="order-item">
                                <span class="item-label">Service Fee:</span>
                                <span class="item-value">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @endforeach
                            <div class="order-item">
                                <span class="item-label">Total:</span>
                                <span class="item-value total-price" data-harga="{{$total}}">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @if($payment->status == 0)
                        <div class="payment-actions">
                            <button class="pay-btn" id="pay-button">Bayar Sekarang</button>
                        </div>
                        @endif
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