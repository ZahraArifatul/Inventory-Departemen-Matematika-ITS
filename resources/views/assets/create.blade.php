@extends('layouts.app')
@section('title', 'Tambah Aset')
@section('content')
<h4 class="mb-3">Tambah Aset</h4>
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('assets.store') }}">
        @csrf
        @include('assets._form')
        <div class="mt-4"><button class="btn btn-primary">Simpan</button> <a href="{{ route('assets.index') }}" class="btn btn-link">Batal</a></div>
    </form>
</div></div>
@endsection
