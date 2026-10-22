@extends('layouts.app')
@section('title', 'Edit Barang')
@section('content')
<h4 class="mb-3">Edit Barang</h4>
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('barang.update', $barang) }}">
        @csrf @method('PUT')
        @include('barang._form')
        <div class="mt-4"><button class="btn btn-primary">Simpan Perubahan</button> <a href="{{ route('barang.show', $barang) }}" class="btn btn-link">Batal</a></div>
    </form>
</div></div>
@endsection
