@extends('layouts.admin')

@section('title', 'Buat Produk Baru - Go Do It')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl mx-auto my-8">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Buat Produk Baru</h2>

    {{-- Session Status --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Produk</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Productable Existing --}}
        <div>
            <label for="productable_mode" class="block text-sm font-medium text-gray-700 mb-1">Apakah Produk Ada?</label>
            <select id="productable_mode" name="productable_mode" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Pilih Opsi</option>
                <option value="new" {{ old('productable_mode') == 'new' ? 'selected' : '' }}>Buat Baru</option>
                <option value="existing" {{ old('productable_mode') == 'existing' ? 'selected' : '' }}>Pilih yang Sudah Ada</option>
            </select>
            @error('productable_mode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Productable Id --}}
        <div>
            <label for="productable_id" class="block text-sm font-medium text-gray-700 mb-1">Produk Id</label>
            <input type="number" id="productable_id" name="productable_id" value="{{ old('productable_id') }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('productable_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Productable Type --}}
        <div>
            <label for="productable_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk</label>
            <select id="productable_type" name="productable_type" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Pilih Tipe</option>
                @foreach($productTypes as $value => $label)
                    <option value="{{ $value }}" {{ old('productable_type') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('productable_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea id="description" name="description" rows="4"
                      class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Price --}}
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
            <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Produk</label>
            <select id="status" name="status"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @foreach(\App\Enums\Product\ProductStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : ($status->value === 'draft' ? 'selected' : '') }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Published At --}}
        <div>
            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Publikasi (Opsional)</label>
            <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at') }}"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika status bukan 'Published' atau ingin publikasi segera.</p>
            @error('published_at') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Buat Produk
            </button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('products.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Kembali ke Daftar Produk
            </a>
        </div>
    </form>
</div>
@endsection