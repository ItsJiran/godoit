@section('title', 'Marketing Kit')
<x-app-layout>
    <section class="hero-title">
        <h1>Marketing Kit</h1>
    </section>
    <!-- SECTION MARKETING KIT -->
    <section class="hero">
        <div class="container">
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
                        <a href="javascript:;" class="btn copy-konten btn-mobile-block"><svg class="copy-icon" viewBox="0 0 24 24">
                            <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"></path>
                        </svg> Copy</a>
                    </div>
                </div>
                @empty
                <b>Kosong!</b>
                @endforelse
            </div>
            <!-- PAGINATION -->
            <x-pagination :paginator="$kits" />
        </div>
    </section>
</x-app-layout>
