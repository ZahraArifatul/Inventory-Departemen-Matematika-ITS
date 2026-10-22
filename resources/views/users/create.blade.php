@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('content')
<h4 class="mb-3">Tambah Pengguna</h4>
<div class="card" style="max-width:520px"><div class="card-body">
    <form method="post" action="{{ route('users.store') }}">@csrf
        @include('users._form')
        <button class="btn btn-primary">Simpan</button> <a href="{{ route('users.index') }}" class="btn btn-link">Batal</a>
    </form>
</div></div>
@endsection
