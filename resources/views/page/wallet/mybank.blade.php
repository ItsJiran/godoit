@section('title', 'My Bank')
<x-app-layout>
    <section class="hero-title">
        <h1>My Bank</h1>
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
                    <form action="{{ route('wallet.savebank') }}" method="POST" class="edit-profile-form">
                        @csrf

                        <div class="form-group">
                            <label for="atas_nama" class="form-label">Atas Nama</label>
                            <input type="text" name="atas_nama" id="atas_nama" class="form-input"
                                   value="{{ old('atas_nama', $bank->atas_nama ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_bank" class="form-label">Nama Bank</label>
                            <input type="text" name="nama_bank" id="nama_bank" class="form-input"
                                   value="{{ old('nama_bank', $bank->nama_bank ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="no_rek" class="form-label">No Rekening</label>
                            <input type="text" name="no_rek" id="no_rek" class="form-input"
                                   value="{{ old('no_rek', $bank->no_rek ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</x-app-layout>
