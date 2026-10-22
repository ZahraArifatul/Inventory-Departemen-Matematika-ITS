@extends('layouts.app')
@section('title', 'Ruangan')
@section('content')
<h4 class="mb-3">Master Data Ruangan</h4>
<div class="row g-3">
    <div class="col-md-5"><div class="card"><div class="card-header bg-white fw-semibold">Tambah Ruangan</div><div class="card-body">
        <form method="post" action="{{ route('ruangan.store') }}">
            @csrf
            <div class="mb-2"><label class="form-label">Nama ruangan *</label><input name="nama" class="form-control" value="{{ old('nama') }}" required></div>
            <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="{{ old('keterangan') }}"></div>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div></div></div>
    <div class="col-md-7"><div class="card"><table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>Jumlah barang</th><th></th></tr></thead>
        <tbody>
        @foreach ($ruangan as $r)
            <tr>
                <td>{{ $r->nama }}@if($r->keterangan)<div class="small text-muted">{{ $r->keterangan }}</div>@endif</td>
                <td>{{ $r->barang_count }}</td>
                <td class="text-end text-nowrap"><div class="d-flex gap-1 justify-content-end">
                    <a href="{{ route('ruangan.edit', $r) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="{{ route('ruangan.destroy', $r) }}" onsubmit="return confirm('Hapus ruangan ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </div></td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
</div>
@endsection
