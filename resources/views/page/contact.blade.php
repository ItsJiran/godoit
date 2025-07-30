@section('title', 'Contact Us')
<x-app-layout>
    <section class="hero-title">
        <h1>Contact Us</h1>
    </section>
    <section class="hero">
        <div class="container min-container">
            <div class="edit-profile-box">
                @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-icon">✓</div>
                    <div class="alert-text">
                        <div class="alert-title">Success!</div>
                        <div class="alert-message">{{ session('success') }}</div>
                    </div>
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">✕</div>
                    <div class="alert-text">
                        <div class="alert-title">Error!</div>
                        <div class="alert-message">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="box-formulir">
                    <form action="{{ route('page.contact.submit') }}" method="POST" class="edit-profile-form">
                        @csrf

                        <div class="form-group">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" name="judul" id="judul" class="form-input" value="{{ old('judul') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-input" value="{{ old('nama') }}" required>
                        </div>

                        @guest
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
                        </div>
                        @endguest

                        <div class="form-group">
                            <label for="whatsapp" class="form-label">Whatsapp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-input" value="{{ old('whatsapp') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea name="pesan" id="pesan" class="form-input" rows="5" required>{{ old('pesan') }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-primary">Kirim Pesan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</x-app-layout>