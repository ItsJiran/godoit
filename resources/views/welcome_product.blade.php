@section('title', 'Godoit.id')
<x-app-layout>
    <section class="hero orange-bg orange-bg-img">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>{{ $product->title }}</h1>
                    <p class="hero-date">{{ \Carbon\Carbon::parse($product->productable->timestamp)->translatedFormat('l, j F Y, (H:iA)') }}</p>
                    <a href="/product/view/{{$product->id}}?reg={{ request('reg') }}" class="btn btn-join">Ikuti Sekarang</a>
                </div>
                <div class="hero-image">
                    <img src="/storage/{{$product->thumbnail->path}}" alt="Program Leader" />
                </div>
            </div>
        </div>
    </section>

    <section class="terms-section">
        <div class="container">
            <h2 class="terms-heading">Syarat Dan Ketentuan</h2>
            <ol class="terms-list">

                                            <li class="terms-list-item">
                        <h3>Persyaratan Usia</h3>
                        <p>Kegiatan bisa diikuti mulai usia 7 tahun.</p>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Fasilitas Termasuk Biaya Pendaftaran</h3>
                        <p>Biaya pendaftaran sudah termasuk:</p>
                                                    <ul>
                                                                <li>2x Snack gratis</li>
                                                                <li>1x Makan</li>
                                                                <li>T-shirt</li>
                                                                <li>Armada (kendaraan yang disediakan)</li>
                                                                <li>Hampers untuk 10 orang pendaftar pertama</li>
                                                                <li>Tiket masuk setiap titik lokasi.</li>
                                                        </ul>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Batas Waktu Pembayaran</h3>
                        <p>Peserta diwajibkan melakukan pembayaran kegiatan paling lambat maksimal H-3 (3 hari sebelum) kegiatan.</p>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Transportasi</h3>
                        <p>Perjalanan akan dilaksanakan menggunakan kendaraan yang telah disediakan Rumah Kebangsaan Pancasila.</p>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Titik Temu</h3>
                        <p>Titik temu di Museum Sumpah Pemuda.</p>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Sesi Zoom Meeting</h3>
                        <p>H-3 (3 hari sebelum) kegiatan akan diadakan Zoom Meeting antara para peserta dengan Panitia Pelaksana.</p>
                                                    </li>
                                                <li class="terms-list-item">
                        <h3>Kebijakan Pembatalan/Pengalihan Dana</h3>
                        <p>Apabila peserta berhalangan sampai H-1 (1 hari sebelum) kegiatan, maka dana yang sudah diberikan akan dialokasikan di event berikutnya.</p>
                                                    </li>
                       
             
            </ol>
        </div>
    </section>

    <!-- SECTION FORMULIR -->
    <section class="hero orange-bg" id="join-now">
        <div class="container min-container">
            <div class="hero-title">
                <h2>Formulir Pendaftaran</h2>
            </div>
            @if (Auth::user() && Auth::user()->acquisitions()->where('product_id', $product->id)->active()->exists())
                <button style='margin:0px auto; display:block;' class="btn-buy" disabled>Already Acquired</button>
                <p style='margin:0px auto; display:block; text-align:center; margin-top:20px;' class="mt-3 text-gray-600">You already have an active acquisition for this product.</p>
            @else
                <form class="box-formulir" action="{{ route('product.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="reg" value="{{ request('reg') }}"> {{-- Use the $harga variable for amount --}}

                    <div class="form-group">
                        <label class="form-label" for="nama">Nama Lengkap</label>
                        <input class="form-input" type="text" id="nama" name="nama" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input class="form-input" type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Telepon/Whatsapp</label>
                        <input class="form-input" type="tel" id="phone" name="phone" required pattern="[0-9]{10,15}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="alamat">Alamat Lengkap</label>
                        <textarea class="form-input" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="umur">Umur</label>
                        <input class="form-input" type="number" id="umur" name="umur" min="1" max="120" required>
                    </div>

                    <div class="form-group">
                        <button class="btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            @endif
        </div>
    </section>
</x-app-layout>