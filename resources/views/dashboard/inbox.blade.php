@extends('layouts.admin')
@section('title', 'Inbox')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Inbox</h1>
    </div>

    <!-- Search -->
    <div class="search-form">
        <form action="{{ route('admin.inbox') }}" method="GET">
            <input type="text" name="search" placeholder="Cari Pesan..." required="" value="{{ $query ?? '' }}">
            <button type="submit">Cari</button>
        </form>
        @if ($query)
            <a href="{{ route('admin.inbox') }}">Reset Pencarian</a>
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
        @forelse($contacts as $contact)
            <div class="unit-list-item">
                <!-- Info Dasar -->
                <div class="unit-item-row">
                    <div class="unit-item-label">Judul:</div>
                    <div class="unit-item-value"><b>{{ $contact->judul }}</b></div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Nama:</div>
                    <div class="unit-item-value">{{ $contact->nama }}</div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Tanggal:</div>
                    <div class="unit-item-value">{{ $contact->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="unit-item-row">
                    <div class="unit-item-label">Pesan:</div>
                    <div class="unit-item-value">{{ $contact->pesan }}</div>
                </div>
            </div>
        @empty
            <div class="unit-empty-state">
                Belum ada data kontak.
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <x-pagination :paginator="$contacts" />
</div>
@endsection