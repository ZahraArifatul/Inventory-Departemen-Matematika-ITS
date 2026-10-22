@extends('layouts.app')
@section('title', 'Edit Catatan Perbaikan')
@section('content')
<h4 class="mb-1">Edit Catatan Perbaikan</h4>
<p class="text-muted">{{ $perbaikan->barang->nama }} ({{ $perbaikan->barang->kode }})</p>
<div class="card" style="max-width:640px"><div class="card-body">
    <form method="post" action="{{ route('perbaikan.update', $perbaikan) }}">
        @csrf @method('PUT')
        @include('perbaikan._fields')
        <div class="mt-3"><button class="btn btn-primary">Simpan Perubahan</button> <a href="{{ route('barang.show', $perbaikan->barang_id) }}" class="btn btn-link">Batal</a></div>
    </form>
    <div class="form-text mt-3">Catatan perbaikan tidak bisa dihapus agar riwayat tetap utuh (audit trail).</div>
</div></div>
@endsection
