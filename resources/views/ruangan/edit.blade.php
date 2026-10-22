@extends('layouts.app')
@section('title', 'Edit Ruangan')
@section('content')
<h4 class="mb-3">Edit Ruangan</h4>
<div class="card" style="max-width:480px"><div class="card-body">
    <form method="post" action="{{ route('ruangan.update', $ruangan) }}">
        @csrf @method('PUT')
        <div class="mb-2"><label class="form-label">Nama ruangan *</label><input name="nama" class="form-control" value="{{ old('nama', $ruangan->nama) }}" required></div>
        <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="{{ old('keterangan', $ruangan->keterangan) }}"></div>
        <button class="btn btn-primary">Simpan</button> <a href="{{ route('ruangan.index') }}" class="btn btn-link">Batal</a>
    </form>
</div></div>
@endsection
