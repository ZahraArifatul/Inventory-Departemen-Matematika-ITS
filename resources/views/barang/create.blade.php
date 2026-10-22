@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('content')
<h4 class="mb-3">Tambah Barang</h4>
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('barang.store') }}">
        @csrf
        @include('barang._form')
        <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a href="{{ route('barang.index') }}" class="btn btn-link">Batal</a></div>
    </form>
</div></div>
@endsection
