@extends('layouts.admin')
@section('title', 'Withdraw Management')
@section('content')
<div class="container">
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
    <div class="unit-list-wrapper unit-tablemodel custom-table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Bank</th>
                    <th>No Rekening</th>
                    <th>Atas Nama</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $wd)
                <tr>
                    <td data-label="Tanggal">{{ $wd->created_at->format('d/m/Y H:i') }}</td>
                    <td data-label="Jumlah">Rp {{ number_format($wd->jumlah, 0, ',', '.') }}</td>
                    <td data-label="Bank">{{ $wd->nama_bank }}</td>
                    <td data-label="No Rekening">{{ $wd->no_rek }}</td>
                    <td data-label="Atas Nama">{{ $wd->atas_nama }}</td>
                    <td data-label="Status">
                        @if($wd->status == 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($wd->status == 'sukses')
                            <span class="status-badge status-success">Sukses</span>
                        @elseif($wd->status == 'ditolak')
                            <span class="status-badge status-rejected">Ditolak</span>
                        @endif
                    </td>
                    <td data-label="Action">
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
                    </td>
                </tr>
                @empty
                <tr class="unit-empty-state">
                    <td colspan="7">
                        Belum ada permintaan withdraw.
                        <br>
                        <small>Tunggu permintaan baru dari user.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <x-pagination :paginator="$withdraws" />
</div>
@endsection
