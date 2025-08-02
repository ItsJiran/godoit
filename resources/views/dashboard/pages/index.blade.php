@extends('layouts.admin') {{-- Sesuaikan dengan layout aplikasi Anda --}}

@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Halaman Section</h1>
    </div>

    <div class="header-controls">
        <button class="unit-add-button" onclick="showNotificationModal()">+ Tambah Section</button>
    </div>

    <!-- Modal -->
    <div class="notification-modal-overlay" id="notificationModal">
        <div class="notification-modal-container">
            <button class="notification-modal-close" onclick="hideNotificationModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <h3 class="notification-modal-title">Pilih Tipe Section</h3>

            @if($landing_type == 'homepage')
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/homepage_description'>Tambah Hero</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/homepage_clients'>Tambah Client</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/homepage_product'>Tambah About Product</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/homepage_testimonials'>Tambah Testimony</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/homepage_faq'>Tambah FAQ</a>
            @endif

            @if($landing_type == 'napak_tilas')
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/napaktilas_hero'>Tambah Hero</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/napaktilas_terms_and_requirement'>Tambah Terms Requirement</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/section_gallery'>Tambah Gallery</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/section_video'>Tambah Video</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/section_image'>Tambah Image</a>
                <a class="unit-add-button" style='width:100%; text-align:center;' href='/admin/page/section/create/{{$landing_type}}/section_contact'>Tambah Contact</a>
            @endif

            {{-- <form action="{{ route('saveProduct') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <p class="notification-modal-message">
                    <div class="form-group">
                        <label class="form-label">Nama Produk</label>
                        <input class="form-input" type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gambar</label>
                        <input class="form-input" type="file" name="gambar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-input" name="description" class="form-control" id="ckeditor" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Produk</label>
                        <input class="form-input" type="number" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Acara</label>
                        <input class="form-input" type="datetime-local" name="timestamp" class="form-control" required>
                    </div>
                </p>
                <div class="notification-modal-actions">
                    <a href="#" class="notification-modal-btn notification-modal-btn-cancel" onclick="hideNotificationModal(); return false;">Cancel</a>
                    <button class="btn-primary" type="submit" class="notification-modal-btn notification-modal-btn-confirm">Simpan</button>
                </div>
            </form> --}}
        </div>
    </div>

    {{-- <!-- Search -->
    <div class="search-form">
        <form action="{{ route('admin.product') }}" method="GET">
            <input type="text" name="search" placeholder="Cari Data Produk..." required="" value="{{ $query ?? '' }}">
            <button type="submit">Cari</button>
        </form>
        @if ($query)
            <a href="{{ route('admin.product') }}">Reset Pencarian</a>
        @endif
    </div> --}}

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
        @forelse($sections as $section)
        <div class="unit-list-item">
            <!-- Data View -->
            <div class="unit-item-row">
                <div class="unit-item-label">Tipe:</div>
                <div class="unit-item-value">{{ $section->type }}</div>
                <div class="unit-item-value">{{ $section->index }}</div>
            </div>
            <div class="unit-action-buttons mobile-action-buttons">
                <a href="{{ route('editSection', $section->id) }}" class="unit-btn unit-btn-edit">Edit</a>
                <form action="{{ route('deleteSection', $section->id) }}" method="POST" class="unit-delete-form">
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
    <x-pagination :paginator="$sections" />
</div>
@endsection