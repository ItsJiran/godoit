@extends('layouts.admin')
@section('title', 'Withdraw Management')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Withdraw Management</h1>
    </div>

    <!-- Search -->
    <div class="search-form">
        <form action="{{ route('admin.withdraw') }}" method="GET">
            <input type="text" name="search" placeholder="Cari Withdraw..." value="{{ $query ?? '' }}">
            <button type="submit">Cari</button>
        </form>
        @if ($query)
            <a href="{{ route('admin.withdraw') }}">Reset Pencarian</a>
        @endif
    </div>

    {{-- Flash Messages --}}
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

    <!-- Withdraw List -->
    <div class="unit-list-wrapper unit-listmodel">
        @forelse($withdraws as $wd)
        <div class="unit-list-item">
            <div class="unit-item-row">
                <div class="unit-item-label">User:</div>
                <div class="unit-item-value">{{ $wd->user->name ?? '-' }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Tanggal:</div>
                <div class="unit-item-value">{{ $wd->created_at->format('d M Y, H:i') }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Jumlah:</div>
                <div class="unit-item-value"><b>Rp {{ number_format($wd->jumlah, 0, ',', '.') }}</b></div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Bank:</div>
                <div class="unit-item-value">{{ $wd->nama_bank }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">No Rekening:</div>
                <div class="unit-item-value">{{ $wd->no_rek }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Atas Nama:</div>
                <div class="unit-item-value">{{ $wd->atas_nama }}</div>
            </div>
            <div class="unit-item-row unit-fullrow">
                <div class="unit-item-label">Status:</div>
                <div class="unit-item-value">
                    @if($wd->status == 'pending')
                        <span style="color: orange;">Pending</span>
                    @elseif($wd->status == 'sukses')
                        <span style="color: green;">Sukses</span>
                    @elseif($wd->status == 'ditolak')
                        <span style="color: red;">Ditolak</span>
                    @endif
                </div>
            </div>

            @if($wd->status == 'pending')
            <div class="unit-action-buttons mobile-action-buttons unit-fullrow">
                <form action="{{ route('admin.withdraw.update', $wd->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <input type="hidden" name="status" value="sukses">
                    <button type="submit" class="unit-btn unit-btn-edit" onclick="return confirm('Terima withdraw ini?')">Terima</button>
                </form>
                <form action="{{ route('admin.withdraw.update', $wd->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <input type="hidden" name="status" value="ditolak">
                    <button type="submit" class="unit-btn unit-btn-delete" onclick="return confirm('Tolak withdraw ini?')">Tolak</button>
                </form>
            </div>
            @endif
        </div>
        @empty
            <div class="unit-empty-state">
                Belum ada permintaan withdraw.
                <br>
                <small>Tunggu permintaan baru dari user.</small>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <x-pagination :paginator="$withdraws" />
</div>
@endsection
