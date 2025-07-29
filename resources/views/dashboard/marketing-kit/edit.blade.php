@extends('layouts.admin')
@section('title', 'Marketing Kit')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Edit Marketing Kit</h1>
    </div>

    <div class="box-formulir">
        <form action="{{ route('updatekit', $kit->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Judul</label>
                <input class="form-input" type="text" name="judul" class="form-control" value="{{ $kit->judul }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Gambar <small>(Kosongkan jika tidak ingin mengubah)</small></label>
                <input class="form-input" type="file" name="gambar" class="form-control">
                <div class="goto-img"><img src="{{ asset('storage/' . $kit->gambar) }}" width="150"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Konten <small>(Gunakan <code>{link_affiliate}</code> untuk posisi link)</small></label>
                <textarea class="form-input" name="konten" class="form-control" id="ckeditor" rows="10" required>{{ $kit->konten }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection