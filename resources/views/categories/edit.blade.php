@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<h4 class="mb-3">Edit Kategori</h4>
<div class="card" style="max-width:480px"><div class="card-body">
    <form method="post" action="{{ route('categories.update', $category) }}">
        @csrf @method('PUT')
        <div class="mb-2"><label class="form-label">Nama *</label><input name="nama" class="form-control" value="{{ old('nama', $category->nama) }}" required></div>
        <div class="mb-3"><label class="form-label">Interval servis default (bulan)</label><input type="number" min="1" name="interval_default_bulan" class="form-control" value="{{ old('interval_default_bulan', $category->interval_default_bulan) }}">
            <div class="form-text">Hanya mengisi otomatis saat menambah aset baru. Aset yang sudah ada tidak berubah.</div></div>
        <button class="btn btn-primary">Simpan</button> <a href="{{ route('categories.index') }}" class="btn btn-link">Batal</a>
    </form>
</div></div>
@endsection
