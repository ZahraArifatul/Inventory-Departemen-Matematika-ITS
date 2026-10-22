@extends('layouts.app')
@section('title', 'Edit Aset')
@section('content')
<h4 class="mb-3">Edit Aset</h4>
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('assets.update', $asset) }}">
        @csrf @method('PUT')
        @include('assets._form')
        <div class="mt-4 d-flex justify-content-between">
            <div><button class="btn btn-primary">Simpan Perubahan</button> <a href="{{ route('assets.show', $asset) }}" class="btn btn-link">Batal</a></div>
        </div>
    </form>
    <form method="post" action="{{ route('assets.destroy', $asset) }}" class="mt-3" onsubmit="return confirm('Hapus aset ini beserta seluruh riwayat servisnya?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Hapus Aset</button>
    </form>
</div></div>
@endsection
