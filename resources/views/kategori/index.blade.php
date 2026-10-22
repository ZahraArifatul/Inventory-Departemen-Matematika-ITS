@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('content')
<h4 class="mb-3">Master Data Kategori Barang</h4>
<div class="row g-3">
    <div class="col-md-5"><div class="card"><div class="card-header bg-white fw-semibold">Tambah Kategori</div><div class="card-body">
        <form method="post" action="{{ route('kategori.store') }}">
            @csrf
            <div class="mb-2"><label class="form-label">Nama *</label><input name="nama" class="form-control" value="{{ old('nama') }}" required></div>
            <div class="mb-3"><label class="form-label">Umur ekonomis (tahun)</label><input type="number" min="1" name="umur_ekonomis_tahun" class="form-control" value="{{ old('umur_ekonomis_tahun') }}">
                <div class="form-text">Perkiraan lama pakai barang di kategori ini. Dipakai untuk analisis kelayakan.</div></div>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div></div></div>
    <div class="col-md-7"><div class="card"><table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>Umur ekonomis</th><th>Jumlah barang</th><th></th></tr></thead>
        <tbody>
        @foreach ($kategori as $k)
            <tr>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->umur_ekonomis_tahun ? $k->umur_ekonomis_tahun . ' tahun' : '-' }}</td>
                <td>{{ $k->barang_count }}</td>
                <td class="text-end text-nowrap"><div class="d-flex gap-1 justify-content-end">
                    <a href="{{ route('kategori.edit', $k) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="{{ route('kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </div></td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
</div>
@endsection
