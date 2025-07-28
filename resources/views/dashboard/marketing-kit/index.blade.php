@extends('layouts.admin')
@section('title', 'Marketing Kit')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Marketing Kit</h1>
    </div>

    <div class="header-controls">
        <button class="unit-add-button" onclick="showNotificationModal()">+ Tambah Data</button>
    </div>

    <!-- Modal -->
    <div class="notification-modal-overlay" id="notificationModal">
        <div class="notification-modal-container">
            <button class="notification-modal-close" onclick="hideNotificationModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <h3 class="notification-modal-title">Tambah Marketing Kit</h3>
            <form action="{{ route('simpankit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <p class="notification-modal-message">
                    <div class="form-group">
                        <label class="form-label">Judul</label>
                        <input class="form-input" type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gambar</label>
                        <input class="form-input" type="file" name="gambar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konten</label>
                        <textarea class="form-input" name="konten" class="form-control" id="ckeditor" rows="5" required></textarea>
                    </div>
                </p>
                <div class="notification-modal-actions">
                    <a href="#" class="notification-modal-btn notification-modal-btn-cancel" onclick="hideNotificationModal(); return false;">Cancel</a>
                    <button class="btn-primary" type="submit" class="notification-modal-btn notification-modal-btn-confirm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search -->
    <div class="search-form">
        <form action="{{ route('marketingkit') }}" method="GET">
            <input type="text" name="search" placeholder="Cari Data Marketing Kit..." required="" value="{{ $query ?? '' }}">
            <button type="submit">Cari</button>
        </form>
        @if ($query)
            <a href="{{ route('marketingkit') }}">Reset Pencarian</a>
        @endif
    </div>

    {{-- Flash message --}}
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
    @if(session('error'))
    <!-- Custom Error -->
    <div class="alert alert-error">
        <div class="alert-icon">✕</div>
        <div class="alert-text">
            <div class="alert-title">Error!</div>
            <div class="alert-message">{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <!-- START -->
    <div class="unit-list-wrapper">
        @forelse($kits as $kit)
        <div class="unit-list-item">
            <!-- Data View -->
            <div class="unit-item-row">
                <div class="unit-item-label">Judul:</div>
                <div class="unit-item-value">{{ $kit->judul }}</div>
            </div>
            <div class="unit-image-full">
                <img src="{{ asset('storage/' . $kit->gambar) }}" width="100">
            </div>
            <div class="unit-action-buttons mobile-action-buttons">
                <a href="{{ route('editkit', $kit->id) }}" class="unit-btn unit-btn-edit">Edit</a>
                <form action="{{ route('hapuskit', $kit->id) }}" method="POST" class="unit-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="unit-btn unit-btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="unit-empty-state">
            Belum ada data.
            <br>
            <small>Klik tombol "Tambah Data" untuk menambahkan data.</small>
        </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <x-pagination :paginator="$kits" />
</div>
@endsection