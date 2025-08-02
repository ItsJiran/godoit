<h2>Payment Sukses</h2>
<p><strong>Order ID:</strong> {{ $payment->id_order }}</p>
<p><strong>Tanggal:</strong> {{ $payment->created_at->format('d M Y H:i') }}</p>

<h3>Customer Details:</h3>
<ul>
    <li>Nama: {{ $customer->first_name }}</li>
    <li>Email: {{ $customer->email }}</li>
    <li>Telepon: {{ $customer->phone }}</li>
    <li>Alamat: {{ $customer->address }}</li>
</ul>

<h3>Produk:</h3>
<ul>
    @foreach ($products as $product)
        <li>{{ $product->name }} - Qty: {{ $product->quantity }} - Harga: Rp{{ number_format($product->price, 0, ',', '.') }}</li>
    @endforeach
</ul>
