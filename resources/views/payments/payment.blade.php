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
                            <button class="pay-btn status-btn" id="check-status-button" data-order-id="{{ $payment->id_order }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M544.1 256L552 256C565.3 256 576 245.3 576 232L576 88C576 78.3 570.2 69.5 561.2 65.8C552.2 62.1 541.9 64.2 535 71L483.3 122.8C439 86.1 382 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6C143.2 199.5 223.3 128 320 128C364.4 128 405.2 143 437.7 168.3L391 215C384.1 221.9 382.1 232.2 385.8 241.2C389.5 250.2 398.3 256 408 256L544.1 256zM573.5 356.5C576 339 563.8 322.8 546.4 320.3C529 317.8 512.7 330 510.2 347.4C496.9 440.4 416.8 511.9 320.1 511.9C275.7 511.9 234.9 496.9 202.4 471.6L249 425C255.9 418.1 257.9 407.8 254.2 398.8C250.5 389.8 241.7 384 232 384L88 384C74.7 384 64 394.7 64 408L64 552C64 561.7 69.8 570.5 78.8 574.2C87.8 577.9 98.1 575.8 105 569L156.8 517.2C201 553.9 258 576 320 576C449 576 555.7 480.6 573.4 356.5z"/></svg> Refresh
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="loader-overlay">
        <div class="spinner"></div>
    </div>
    
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
                        $(".loader-overlay").addClass('shows');
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
                        $(".loader-overlay").addClass('shows');
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
                        $(".loader-overlay").addClass('shows');
                        console.log('Status updated: ', data);
                        window.location.href = '/payment/status/{{ $payment->id }}';
                    });
                },
                onClose: function(){
                    alert('Kamu menutup tanpa menyelesaikan pembayaran!');
                }
            });
        });

        // UPDATE MANUAL
        var checkStatusButton = document.getElementById('check-status-button');
        checkStatusButton.addEventListener('click', function () {
            var orderId = this.getAttribute('data-order-id');
            $.post("{{ route('payment.check-status') }}", {
                _token: '{{ csrf_token() }}',
                order_id: orderId
            }, function(response) {
                console.log('Status dari Midtrans:', response.midtrans_status);
                console.log('Status di database:', response.data.status);
                $(".loader-overlay").addClass('shows');
                window.location.href = '/payment/status/{{ $payment->id }}';
            }).fail(function(xhr, status, error) {
                console.error('Gagal memperbarui status:', error);
                alert('Gagal memperbarui status. Cek konsol untuk detail.');
            });
        });
    </script>
</x-app-layout>