@extends('layouts.app')
@section('title', 'Edit Riwayat Servis')
@section('content')
<h4 class="mb-1">Edit Riwayat Servis</h4>
<p class="text-muted">{{ $maintenance->asset->nama }} ({{ $maintenance->asset->kode }})</p>
<div class="card" style="max-width:640px"><div class="card-body">
    <form method="post" action="{{ route('maintenances.update', $maintenance) }}">
        @csrf @method('PUT')
        @include('maintenances._fields')
        <div class="mt-3"><button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('assets.show', $maintenance->asset_id) }}" class="btn btn-link">Batal</a></div>
    </form>
</div></div>
@endsection
