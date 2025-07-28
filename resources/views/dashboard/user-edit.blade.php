@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Edit User</h1>
    </div>

    <div class="box-formulir">
        <form action="{{ route('adminedituser', $user->id) }}" method="POST" class="edit-profile-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
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
@endsection