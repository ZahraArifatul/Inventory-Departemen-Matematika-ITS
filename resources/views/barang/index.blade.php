@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Barang Inventaris</h4>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Cetak / PDF</button>
        @if (auth()->user()->bisaKelola())
            <a href="{{ route('barang.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Barang</a>
        @endif
    </div>
</div>

<form class="row g-2 mb-3 no-print" method="get">
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode / nama / spesifikasi"></div>
    <div class="col-md-2"><select name="kategori_id" class="form-select"><option value="">Semua kategori</option>@foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></div>
    <div class="col-md-3"><select name="ruangan_id" class="form-select"><option value="">Semua ruangan</option>@foreach ($daftarRuangan as $r)<option value="{{ $r->id }}" @selected(request('ruangan_id') == $r->id)>{{ $r->nama }}</option>@endforeach</select></div>
    <div class="col-md-2"><select name="kondisi" class="form-select"><option value="">Semua kondisi</option>@foreach (\App\Models\Barang::KONDISI as $kode => $label)<option value="{{ $kode }}" @selected(request('kondisi') === $kode)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode</th><th>Nama</th><th>Ruangan</th><th>Kondisi</th><th>Tgl Pengadaan</th><th class="text-end">Nilai Awal</th><th class="text-end">Perbaikan</th><th class="no-print"></th></tr></thead>
            <tbody>
            @forelse ($barang as $b)
                <tr class="{{ $b->kondisi === 'dihapus' ? 'text-muted' : '' }}">
                    <td>{{ $b->kode }}</td>
                    <td><a href="{{ route('barang.show', $b) }}" class="fw-semibold text-decoration-none">{{ $b->nama }}</a><div class="small text-muted">{{ $b->kategori->nama }}</div></td>
                    <td>{{ $b->ruangan->nama }}</td>
                    <td><span class="badge text-bg-{{ $b->warna_kondisi }}">{{ $b->label_kondisi }}</span></td>
                    <td class="text-nowrap">{{ \App\Support\Fmt::tgl($b->tanggal_pengadaan) }}</td>
                    <td class="text-end text-nowrap">{{ \App\Support\Fmt::rp($b->nilai_awal) }}</td>
                    <td class="text-end text-nowrap">{{ $b->perbaikan_count }}x<div class="small text-muted">{{ \App\Support\Fmt::rp($b->perbaikan_sum_biaya ?? 0) }}</div></td>
                    <td class="text-end no-print"><a href="{{ route('barang.show', $b) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada barang.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $barang->links() }}</div>
@endsection
