@extends('layouts.admin')
@section('title', 'Marketing Kit')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Edit Marketing Kit</h1>
    </div>

    <div class="box-formulir">
        <form action="{{ route('updateProduct', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Judul</label>
                <input class="form-input" type="text" name="title" class="form-control" value="{{ $product->title }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Gambar (Kosongkan jika tidak ingin mengubah)</label>
                <input class="form-input" type="file" name="gambar" class="form-control">
                <div class="goto-img"><img style="width:90%" src="{{ $product->thumbnail ?  asset('storage/' . $product->thumbnail->path) : '' }}"></div>                
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-input" name="description" class="form-control" id="ckeditor" rows="10" required>{{ $product->description }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Harga Produk</label>
                <input class="form-input" type="number" name="price" class="form-control" value="{{ $product->price }}" required>                
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Acara</label>
                <input class="form-input" type="datetime-local" name="timestamp" class="form-control" value="{{ $product->productable->timestamp }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection