@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<h4 class="mb-3">Edit Kategori</h4>
<div class="card" style="max-width:480px"><div class="card-body">
    <form method="post" action="{{ route('kategori.update', $kategori) }}">
        @csrf @method('PUT')
        <div class="mb-2"><label class="form-label">Nama *</label><input name="nama" class="form-control" value="{{ old('nama', $kategori->nama) }}" required></div>
        <div class="mb-3"><label class="form-label">Umur ekonomis (tahun)</label><input type="number" min="1" name="umur_ekonomis_tahun" class="form-control" value="{{ old('umur_ekonomis_tahun', $kategori->umur_ekonomis_tahun) }}"></div>
        <button class="btn btn-primary">Simpan</button> <a href="{{ route('kategori.index') }}" class="btn btn-link">Batal</a>
    </form>
</div></div>
@endsection
