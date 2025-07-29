@section('title', 'My Profile')
<x-app-layout>
    <section class="hero-title">
        <h1>My Profile</h1>
    </section>
    <section class="hero">
        <div class="container min-container">
            <div class="edit-profile-box">
                @if(session('success'))
                <!-- Success -->
                <div class="alert alert-success">
                    <div class="alert-icon">✓</div>
                    <div class="alert-text">
                        <div class="alert-title">Success!</div>
                        <div class="alert-message">{{ session('success') }}</div>
                    </div>
                </div>
                @endif
                @if ($errors->any())
                <!-- Error -->
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
                    <form action="{{ route('profile.update') }}" method="POST" class="edit-profile-form" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="profile-section">
                            <div class="profile-avatar" style="border-radius:100%; width:120px; aspect-ratio:1; margin:0px auto; background-color:gray; overflow:hidden;">
                                <img style="width:100%" src="{{ $user->avatar ?  asset('storage/' . $user->avatar->conversions['medium']['path']) : '' }}">                                            
                            </div>                     
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Foto Profile (Kosongkan jika tidak ingin mengubah)</label>
                            <input class="form-input" type="file" id='avatar' name="avatar" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-input" value="{{ old('username', $user->username) }}" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp" class="form-label">Whatsapp</label>
                            <input type="number" name="whatsapp" id="whatsapp" class="form-input" value="{{ old('whatsapp', $user->whatsapp) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="kota" class="form-label">Kota</label>
                            <input type="text" name="kota" id="kota" class="form-input" value="{{ old('kota', $user->kota) }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
