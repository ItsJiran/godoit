@section('title', 'Withdraw')
<x-app-layout>
    <section class="hero-title">
        <h1>Withdraw</h1>
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

                @if(session('error'))
                <div class="alert alert-error">
                    <div class="alert-icon">✕</div>
                    <div class="alert-text">
                        <div class="alert-title">Error!</div>
                        <div class="alert-message">{{ session('error') }}</div>
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
                    
                    <p><strong>Saldo Tersedia:</strong> Rp {{ number_format($saldo, 0, ',', '.') }}</p>
                    <p><strong>Saldo Penarikan Pending:</strong> Rp {{ number_format($saldoPenarikanPending, 0, ',', '.') }}</p>

                    <div class="bank-info">
                        <p><strong>Bank:</strong> {{ $bank->nama_bank }}</p>
                        <p><strong>No Rekening:</strong> {{ $bank->no_rek }}</p>
                        <p><strong>Atas Nama:</strong> {{ $bank->atas_nama }}</p>
                    </div>

                    <form action="{{ route('wallet.withdraw.store') }}" method="POST" class="edit-profile-form">
                        @csrf

                        <div class="form-group">
                            <label for="jumlah" class="form-label">Jumlah Penarikan</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-input"
                                   value="{{ old('jumlah') }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password Akun</label>
                            <input type="password" name="password" id="password" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-primary">Ajukan Penarikan</button>
                        </div>
                    </form>
                </div>

                {{-- Riwayat Withdraw --}}
                @if($riwayat->count() > 0)
                <div class="withdraw-history">
                    <h2>Riwayat Withdraw</h2>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Bank</th>
                                <th>No Rekening</th>
                                <th>Atas Nama</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $item)
                            <tr>
                                <td data-label="Tanggal">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td data-label="Jumlah">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                <td data-label="Bank">{{ $item->nama_bank }}</td>
                                <td data-label="No Rekening">{{ $item->no_rek }}</td>
                                <td data-label="Atas Nama">{{ $item->atas_nama }}</td>
                                <td data-label="Status">
                                    @if($item->status == 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($item->status == 'sukses')
                                        <span class="status-badge status-success">Sukses</span>
                                    @elseif($item->status == 'ditolak')
                                        <span class="status-badge status-rejected">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </section>
</x-app-layout>