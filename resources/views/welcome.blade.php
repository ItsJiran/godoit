@section('title', 'Godoit.id')
<x-app-layout>


    @forelse($sections as $section)

        @if($section->type == 'homepage_description')

        <section class="hero orange-bg orange-bg-img">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>{{ $section->meta_content['title'] }}</h1>
                        <p>{!! nl2br($section->meta_content['description']) !!}</p>
                        @if(Auth::check())
                            <a href="{{ '/page/napak_tilas' . '?reg=' . request('reg') }}" class="btn btn-join">Selengkapnya</a>
                        @endif
                        @if(!Auth::check() && array_key_exists('button_register',$section->meta_content))
                        <a href="{{ $section->meta_content['button_register']['href'] . '?reg=' . request('reg') }}" class="btn btn-join btn-register">Daftar Sekarang</a>
                        @endif
                    </div>
                    <div class="hero-image">
                        <img src="{{  $section->meta_content['hero_image'] }}" alt="People collaborating and connecting">
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if($section->type == 'homepage_clients')
            <!-- Client Section -->
            <section class="clients">
                <div class="container">
                    <h2 class="clients-heading">{{$section->meta_content['title']}}</h2>
                    <div class="client-grid">
                        @forelse($section->meta_content['content'] as $client)
                            <div class="client-logo">
                                <img src="{{$client['src']}}">
                            </div>
                            @empty
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        @if($section->type == 'homepage_product')

            <section class="hero">
                <div class="container">
                    <div class="mysection-wrapper reverse">
                        <div class="mysection-item reverse">
                            <div class="mysection-image">
                                @if($products[0] != null)
                                    <img src="/storage/{{$products[0]->thumbnail->path}}" alt="Program Leader" />
                                @endif

                                @if($products[0] == null)
                                    <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program"/>
                                @endif
                            </div>
                            <div class="mysection-content">
                                {{-- <h3>Event yang akan datang</h3> --}}
                                <h3 class="landing-label homepage_product_label">{{ $section->meta_content['subtitle'] }}</h3>
                                <h2>{{ $section->meta_content['title'] }}</h2>
                                @if($products[0] != null)
                                    <p class="hero-date">{{ \Carbon\Carbon::parse($products[0]->productable->timestamp)->translatedFormat('l, j F Y, (H:iA)') }}</p>                                    
                                @endif
                                <p>{!! nl2br($section->meta_content['description']) !!}</p>
                                @if(array_key_exists('button_more',$section->meta_content))
                                    <a href="{{ $section->meta_content['button_more']['href'] . '?reg=' . request('reg') }}" class="btn btn-join">Selengkapnya</a>
                                @endif
                            </div>
                        </div>
                            
                    </div>
                </div>
            </section>
        @endif

        @if($section->type == 'homepage_testimonials')
        <section class="testimonials">
            <div class="container">
                <h2 class="testimonials-heading">{{ $section->meta_content['title'] }}</h2>

                <div class="owl-carousel owl-theme">
                    @forelse($section->meta_content['content'] as $testimony)
                    <div class="testimonial-card item">
                        <p class="testimonial-quote">{{$testimony['quote']}}</p>
                        <div class="testimonial-author">
                            <img class="author-logo" src="{{$testimony['src']}}" alt="Logo A">
                            <div class="author-info">
                                <h4>{{$testimony['name']}}</h4>
                                <p>{{$testimony['role']}}</p>
                            </div>
                        </div>
                    </div>
                        @empty 
                    @endforelse
                </div>                    
            </div>
        </section>
        @endif

        @if($section->type == 'homepage_faq')
            <!-- FAQ Section -->
            <section class="faq">
                <div class="container">
                    <h2 class="faq-heading">{{ $section->meta_content['title'] }}</h2>
                    <div class="faq-container">
                        @forelse($section->meta_content['content'] as $faq)
                        <div class="faq-item">
                            <div class="faq-question">{{$faq['question']}}</div>
                            <div class="faq-answer">
                                <p>{{$faq['answer']}}</p>
                            </div>
                        </div>
                            @empty 
                        @endforelse                        
                    </div>
                </div>
            </section>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const faqQuestions = document.querySelectorAll('.faq-question');
                    faqQuestions.forEach(question => {
                        question.addEventListener('click', () => {
                            const activeQuestion = document.querySelector('.faq-question.active');
                            if (activeQuestion && activeQuestion !== question) {
                                activeQuestion.classList.remove('active');
                                activeQuestion.nextElementSibling.classList.remove('active');
                            }
                            question.classList.toggle('active');
                            const answer = question.nextElementSibling;
                            answer.classList.toggle('active');
                        });
                    });
                });
            </script>
        @endif

        @empty
    @endforelse

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