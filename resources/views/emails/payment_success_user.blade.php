<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Sukses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,0 1000,0 1000,60 0,100"/></svg>');
            background-size: cover;
        }
        
        .success-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            text-align:center;
            line-height:60px;
            margin: 0 auto 15px;
            position: relative;
            z-index: 1;
            font-size: 30px;
            font-weight: bold;
            color: white;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 30px;
        }
        
        .order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #4CAF50;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 0 5px 5px 0;
        }
        
        .order-info h2 {
            color: #4CAF50;
            font-size: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .order-info h2::before {
            content: '📄';
            margin-right: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
        }
        
        .customer-section h3::before {
            content: '👤';
            margin-right: 10px;
        }
        
        .products-section h3::before {
            content: '🛍️';
            margin-right: 10px;
        }
        
        .customer-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .customer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }
        
        .customer-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .customer-item strong {
            min-width: 70px;
            color: #555;
        }
        
        .products-list {
            list-style: none;
        }
        
        .product-item {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.3s ease;
        }
        
        .product-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .product-details {
            font-size: 14px;
            color: #666;
        }
        
        .product-price {
            font-weight: bold;
            color: #4CAF50;
            font-size: 16px;
        }
        
        .total-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }
        
        .footer p {
            margin-bottom: 10px;
        }
        
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            
            .customer-grid {
                grid-template-columns: 1fr;
            }
            
            .product-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Pembayaran Berhasil!</h1>
            <p>Terima kasih atas pembelian Anda</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Order Information -->
            <div class="order-info">
                <h2>Informasi Pesanan</h2>
                <div class="info-grid">
                    <div class="info-label">Order ID: <strong>{{ $payment->id_order }}</strong></div>
                    <div class="info-label">Tanggal: {{ $payment->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
            
            <!-- Customer Details -->
            <div class="section customer-section">
                <h3>Detail Customer</h3>
                <div class="customer-details">
                    <div class="customer-grid">
                        <div class="customer-item">
                            <strong>Nama:</strong> {{ $customer->first_name }}
                        </div>
                        <div class="customer-item">
                            <strong>Email:</strong> {{ $customer->email }}
                        </div>
                        <div class="customer-item">
                            <strong>Telepon:</strong> {{ $customer->phone }}
                        </div>
                        <div class="customer-item">
                            <strong>Alamat:</strong> {{ $customer->address }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products -->
            <div class="section products-section">
                <h3>Produk yang Dibeli</h3>
                <ul class="products-list">
                    @php $total_amount=0 @endphp
                    @foreach ($products as $product)
                        <li class="product-item">
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-details">Quantity: {{ $product->quantity }}</div>
                            </div>
                            <div class="product-price">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </div>
                        </li>
                        @php $total_amount = $product->price+$total_amount; @endphp
                    @endforeach
                </ul>
            </div>
            
            <!-- Optional Total Section (if you have total amount) -->
            <div class="total-section">
                <p>Total Pembayaran</p>
                <div class="total-amount">Rp{{ number_format($total_amount, 0, ',', '.') }}</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah berbelanja dengan kami!</p>
            <p>Jika ada pertanyaan, silakan hubungi <a href="mailto:support@example.com">support@example.com</a></p>
            <p>&copy; 2025 Godoit. All rights reserved.</p>
        </div>
    </div>
</body>
</html>