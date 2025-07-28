@extends('layouts.admin')
@section('title', 'All Users')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>All Users</h1>
    </div>

    <!-- Search -->
    <div class="search-form">
        <form action="{{ route('marketingkit') }}" method="GET">
            <input type="text" name="search" placeholder="Cari User..." required="" value="{{ $query ?? '' }}">
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
        @forelse($users as $user)
        <div class="unit-list-item">
            <!-- Data View -->
            <div class="unit-item-row">
                <div class="unit-item-label">Nama:</div>
                <div class="unit-item-value"><b>{{ $user->name }}</b>
                @if($user->deleted_at != NULL)<span class="unit-label label-danger">Blocked</span>@endif</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Email:</div>
                <div class="unit-item-value">{{ $user->email }}</div>
            </div>
            <div class="unit-item-row">
                <div class="unit-item-label">Whatsapp:</div>
                <div class="unit-item-value">{{ $user->whatsapp }}</div>
            </div>
            @if($user->deleted_at == NULL)
            <div class="unit-action-buttons mobile-action-buttons">
                <a href="{{ route('edituser', $user->id) }}" class="unit-btn unit-btn-edit">Edit</a>
                <form action="{{ route('blokiruser', $user->id) }}" method="POST" class="unit-delete-form">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="unit-btn unit-btn-delete" onclick="return confirm('Apakah Anda yakin ingin memblokir?')">Blokir</button>
                </form>
            </div>
            @else
            <div class="unit-action-buttons mobile-action-buttons unit-full-buttons">
                <form action="{{ route('unblokiruser', $user->id) }}" method="POST" class="unit-delete-form">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="unit-btn unit-btn-delete" onclick="return confirm('Apakah Anda yakin ingin lepas blokir?')">Unblock</button>
                </form>
            </div>
            @endif
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
    <x-pagination :paginator="$users" />
</div>
@endsection