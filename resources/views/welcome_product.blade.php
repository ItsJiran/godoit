@section('title', 'Godoit.id')
<x-app-layout>
    <section class="hero">
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

    <!-- SECTION -->
    <section class="hero">
        <div class="container">
            <div class="mysection-wrapper">
                <div class="mysection-item">
                    <div class="mysection-image">
                        <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program"/>
                    </div>
                    <div class="mysection-content">
                        <h2 class="mysection-title">Program Napak Tilas Kebangsaan</h2>
                        <p class="mysection-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit rhoncus gravida. Proin consectetur congue erat, quis finibus lectus posuere at. Donec sed augue eu est malesuada condimentum.</p>
                        <a href="#join-now" class="btn">Ikuti Sekarang</a>
                    </div>
                </div>

                <div class="mysection-item reverse">
                    <div class="mysection-image">
                        <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program"/>
                    </div>
                    <div class="mysection-content">
                        <h2 class="mysection-title">Program Napak Tilas Kebangsaan</h2>
                        <div class="mysection-price">
                            <small>Rp</small> {{ number_format($product->price ?? 0, 0, ',', '.') }}
                        </div>
                        <p class="mysection-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit rhoncus gravida. Proin consectetur congue erat, quis finibus lectus posuere at. Donec sed augue eu est malesuada condimentum.</p>
                        <a href="#join-now#" class="btn">Ikuti Sekarang</a>
                    </div>
                </div>
            </div>
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