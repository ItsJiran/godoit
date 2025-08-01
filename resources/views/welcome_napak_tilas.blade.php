@section('title', 'Godoit.id')
<x-app-layout>


    @forelse($sections as $section)

        @if($section->type == 'napaktilas_hero')

        <section class="hero orange-bg orange-bg-img">
            <div class="container">
                <div class="hero-content" style="justify-content: center; display:flex; max-width:800px; text-align:center; margin:0px auto;">
                    <div class="hero-text">
                        <img style="width:100%" src="{{  $section->meta_content['hero_image'] }}" alt="People collaborating and connecting">
                        <h1>{{ $section->meta_content['title'] }}</h1>
                        <p>{!! nl2br($section->meta_content['description']) !!}</p>
                        @if($products[0] != null)
                            <div class="terms-button-wrapper">
                                <a href="/product/{{$products[0]->id}}?reg={{ request('reg') }}" class="btn-primary">Lihat Kegiatan Terbaru</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

 
        @endif
        
        @if($section->type == 'napaktilas_terms_and_requirement')

            <!-- Bagian Syarat & Ketentuan (NEW LIST STYLE) -->
            <section class="terms-section">
                <div class="container">
                    <h2 class="terms-heading">{{ $section->meta_content['title'] }}</h2>
                    <ol class="terms-list">

                        @forelse($section->meta_content['content'] as $faq)
                            <li class="terms-list-item">
                                <h3>{{ $faq['title'] }}</h3>
                                <p>{{ $faq['description'] }}</p>
                                @if(array_key_exists('details', $faq))
                            <ul>
                                    @forelse($faq['details'] as $item)
                                    <li>{{$item}}</li>
                                    @empty 
                                    @endforelse
                            </ul>
                                @endif
                            </li>
                            @empty 
                        @endforelse   
                     
                    </ol>
                    @if($products[0] != null)
                        <div class="terms-button-wrapper">
                            <a href="/product/{{$products[0]->id}}?reg={{ request('reg') }}" class="btn-primary">Lihat Kegiatan Terbaru</a>
                        </div>
                    @endif
                </div>
            </section>

        @endif

        @if($section->type == 'section_gallery')

            <section class="gallery-section">
                <div class="container">
                    <h2 class="gallery-heading">{{ $section->meta_content['title'] }}</h2>
                    <div class="gallery-grid">
                        @forelse($section->meta_content['content'] as $faq)
                        <div class="gallery-item">
                            <img src="{{$faq}}">
                        </div>
                        @empty 
                        @endforelse                           
                    </div>
                </div>
            </section>

            <!-- Modal for Zoomed Image -->
            <div id="imageModal" class="modal">
                <span class="close-button">&times;</span>
                <img class="modal-content" id="zoomedImage">
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Skrip untuk Gallery Zoom Feature
                    const modal = document.getElementById("imageModal");
                    const modalImg = document.getElementById("zoomedImage");
                    const galleryItems = document.querySelectorAll('.gallery-item img');
                    const closeButton = document.querySelector('.close-button');

                    // Menambahkan event listener ke setiap gambar di galeri
                    galleryItems.forEach(item => {
                        item.addEventListener('click', function() {
                            modal.style.display = "flex";
                            modalImg.src = this.src;
                        });
                    });

                    // Menutup modal saat tombol 'x' diklik
                    closeButton.addEventListener('click', function() {
                        modal.style.display = "none";
                    });

                    // Menutup modal saat mengklik di luar gambar
                    modal.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            modal.style.display = "none";
                        }
                    });
                });
            </script>

        @endif

        @if($section->type == 'section_video')

        <section class="video-section">
            <div class="container">
                <h2 class="video-heading">{{$section->meta_content['title']}}</h2>
                @if(array_key_exists('description',$section->meta_content))
                <p style="text-align: center">{{$section->meta_content['description']}}</p>
                @endif
                <div class="video-container">
                    <iframe
                        src="{{$section->meta_content['url']}}"
                        title="Video Kegiatan"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </section>

        @endif 

        @if($section->type == 'section_image')
        <section class="gallery-section">
            <div class="container">
                <h2  class="video-heading">{{$section->meta_content['title']}}</h2>
                @if(array_key_exists('description',$section->meta_content))
                <p style="text-align: center">{{$section->meta_content['description']}}</p>
                @endif
                <div class="image-container">
                    <div class="gallery-item">
                        <img src="{{$section->meta_content['url']}}">
                    </div>
                </div>
            </div>
        </section>

        @endif 

        @if($section->type == 'section_contact')
        <section class="section-contact">
            <div class="container">
                <h2  class="video-heading">{{$section->meta_content['title']}}</h2>
                <p style="text-align:center;">{!! nl2br($section->meta_content['content']) !!}</p>        
            </div>
        </section>
        @endif


        @empty
    @endforelse


    <div class="hero orange-bg orange-bg-img" style="display:flex; flex-direction:column">
        <h2  class="video-heading" style='color:white; margin-bottom:30px;'>Daftar Kegiatan</h2>

            <div class="container">
                <div class="page-product-grid">

            @forelse($products as $product)
                <div class="page-product-card">
                    <a href="/product/{{$product->id}}?reg={{ request('reg') }}">
                    <img src="/storage/{{$product->thumbnail->path}}" alt="{{ $product->title }}">
                </a>

                    <div class="page-card-content">
                        <h2 class="page-card-title">{{ $product->title }}</h2>
                        <p class="page-card-date">Kegiatan Untuk : {{ \Carbon\Carbon::parse($product->productable->timestamp)->translatedFormat('l, j F Y, (H:iA)') }}</p>
                    </div>
                </div>
            @empty
            <b>Kosong!</b>
            @endforelse 
        </div>
    </div>
    </div>


    {{-- @forelse($products as $product)
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>{{ $product->title }}</h1>
                    <p class="hero-date">{{ \Carbon\Carbon::parse($product->productable->timestamp)->translatedFormat('l, j F Y, (H:iA)') }}</p>
                    <a href="/product/{{$product->id}}?reg={{ request('reg') }}" class="btn btn-join">Ikuti Sekarang</a>
                </div>
                <div class="hero-image">
                    <img src="/storage/{{$product->thumbnail->path}}" alt="Program Leader" />
                </div>
            </div>
        </div>
    </section>
    @empty
    <b>Kosong!</b>
    @endforelse --}}

    <!-- SECTION -->
    {{-- <section class="hero">
        <div class="container">
            <div class="mysection-wrapper">
                <div class="mysection-item">
                    <div class="mysection-image">
                        <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program"/>
                    </div>
                    <div class="mysection-content">
                        <h2 class="mysection-title">Tentang Sistem Program Godoit</h2>
                        <p class="mysection-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit rhoncus gravida. Proin consectetur congue erat, quis finibus lectus posuere at. Donec sed augue eu est malesuada condimentum.</p>
                        <a href="#join-now" class="btn">Selengkapnya</a>
                    </div>
                </div>

                <div class="mysection-item reverse">
                    <div class="mysection-image">
                        <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program"/>
                    </div>
                    <div class="mysection-content">
                        <h2 class="mysection-title">Tentang Sistem Program Godoit</h2>
                        <div class="mysection-price">Rp 50.000</div>
                        <p class="mysection-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit rhoncus gravida. Proin consectetur congue erat, quis finibus lectus posuere at. Donec sed augue eu est malesuada condimentum.</p>
                        <a href="#join-now" class="btn">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- SECTION MARKETING KIT -->
    <section class="hero">
        <div class="container">
            <div class="hero-title">
                <h2>Marketing Kit</h2>
            </div>
            <div class="program-grid">
                @forelse($kits as $kit)
                <div class="program-card">
                    <div class="program-image">
                        <img src="{{ asset('storage/' . $kit->gambar) }}" alt="{{ $kit->judul }}">
                    </div>
                    <div class="program-content">
                        <h3 class="program-title">{{ $kit->judul }}</h3>
                        <p class="program-description">Salin copywriting dan link affiliasi anda ini ⬇️⬇️⬇️</p>
                        @auth
                        <textarea class="form-input this-konten hidden" name="konten" class="form-control" rows="10" readonly>{!! str_replace('{link_affiliate}', $userReferral, $kit->konten) !!}</textarea>
                        @endauth
                        @guest
                        <textarea class="form-input this-konten hidden" name="konten" class="form-control" rows="10" readonly>{!! str_replace('{link_affiliate}', url('/'), $kit->konten) !!}</textarea>
                        @endguest
                        <a href="javascript:;" class="btn copy-konten"><svg class="copy-icon" viewBox="0 0 24 24">
                            <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"></path>
                        </svg> Copy</a>
                    </div>
                </div>
                @empty
                <b>Kosong!</b>
                @endforelse
            </div>
        </div>
    </section> --}}
</x-app-layout>