@extends('layouts.app')
@section('title', 'Riwayat Perpindahan')
@section('content')
@php $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Riwayat Perpindahan Barang</h4>
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="bi bi-printer"></i> Cetak / PDF</button>
</div>

<form class="row g-2 mb-3 no-print" method="get">
    <div class="col-6 col-md-2"><select name="bulan" class="form-select"><option value="">Semua bulan</option>@foreach ($namaBulan as $no => $nm)<option value="{{ $no }}" @selected(request('bulan') == $no)>{{ $nm }}</option>@endforeach</select></div>
    <div class="col-6 col-md-2"><select name="tahun" class="form-select"><option value="">Semua tahun</option>@foreach ($tahunList as $th)<option @selected(request('tahun') == $th)>{{ $th }}</option>@endforeach</select></div>
    <div class="col-md-3"><select name="ruangan_id" class="form-select"><option value="">Semua ruangan</option>@foreach ($daftarRuangan as $r)<option value="{{ $r->id }}" @selected(request('ruangan_id') == $r->id)>{{ $r->nama }}</option>@endforeach</select></div>
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode / nama barang"></div>
    <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('mutasi.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a></div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Barang</th><th>Asal</th><th>Tujuan</th><th>Tanggal Masuk</th><th>Tanggal Keluar</th><th>Petugas</th></tr></thead>
        <tbody>
        @forelse ($mutasi as $m)
            <tr>
                <td><a href="{{ route('barang.show', $m->barang_id) }}" class="fw-semibold text-decoration-none">{{ $m->barang->nama }}</a><div class="small text-muted">{{ $m->barang->kode }}</div></td>
                <td>{{ $m->asal?->nama ?? '-' }}</td>
                <td class="fw-semibold">{{ $m->tujuan->nama }}@if($m->keterangan)<div class="small text-muted fw-normal">{{ $m->keterangan }}</div>@endif</td>
                <td class="text-nowrap">{{ \App\Support\Fmt::tgl($m->tanggal_masuk) }}</td>
                <td class="text-nowrap">{!! $m->tanggal_keluar ? e(\App\Support\Fmt::tgl($m->tanggal_keluar)) : '<span class="badge text-bg-primary">Masih di sini</span>' !!}</td>
                <td>{{ $m->petugas?->name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $mutasi->links() }}</div>
@endsection
