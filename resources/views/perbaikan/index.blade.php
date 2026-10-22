@extends('layouts.app')
@section('title', 'Riwayat Perbaikan')
@section('content')
@php
    $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $adaFilter = collect(request()->only(['q','bulan','tahun','kategori_id','ruangan_id']))->filter()->isNotEmpty();
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Riwayat Perbaikan</h4>
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="bi bi-printer"></i> Cetak / PDF</button>
</div>

<form class="card card-body mb-3 no-print" method="get">
    <div class="row g-2">
        <div class="col-6 col-md-2"><select name="bulan" class="form-select"><option value="">Semua bulan</option>@foreach ($namaBulan as $no => $nm)<option value="{{ $no }}" @selected(request('bulan') == $no)>{{ $nm }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select name="tahun" class="form-select"><option value="">Semua tahun</option>@foreach ($tahunList as $th)<option @selected(request('tahun') == $th)>{{ $th }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select name="kategori_id" class="form-select"><option value="">Semua kategori</option>@foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></div>
        <div class="col-6 col-md-3"><select name="ruangan_id" class="form-select"><option value="">Semua ruangan</option>@foreach ($daftarRuangan as $r)<option value="{{ $r->id }}" @selected(request('ruangan_id') == $r->id)>{{ $r->nama }}</option>@endforeach</select></div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari barang / kerusakan">
            <button class="btn btn-primary"><i class="bi bi-funnel"></i></button>
            @if ($adaFilter)<a href="{{ route('perbaikan.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>@endif
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Jumlah perbaikan</div><div class="fs-3 fw-bold">{{ $jumlah }}</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Total biaya</div><div class="fs-3 fw-bold">{{ \App\Support\Fmt::rp($totalBiaya) }}</div></div></div></div>
    <div class="col-12 col-md-6"><div class="card h-100"><div class="card-body py-2">
        <div class="text-muted small mb-1">Barang paling sering diperbaiki{{ $adaFilter ? ' (sesuai filter)' : '' }}</div>
        @forelse ($teratas as $t)
            <div class="d-flex justify-content-between small"><a href="{{ route('barang.show', $t->barang_id) }}" class="text-decoration-none">{{ $t->barang->nama }}</a><span>{{ $t->jumlah }}x · {{ \App\Support\Fmt::rp($t->total_biaya) }}</span></div>
        @empty
            <div class="small text-muted">-</div>
        @endforelse
    </div></div></div>
</div>

<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Tanggal</th><th>Barang</th><th>Kerusakan</th><th>Perbaikan</th><th>Penanganan</th><th class="text-end">Biaya</th></tr></thead>
        <tbody>
        @forelse ($perbaikan as $p)
            <tr>
                <td class="text-nowrap">{{ \App\Support\Fmt::tgl($p->tanggal_perbaikan) }}</td>
                <td><a href="{{ route('barang.show', $p->barang_id) }}" class="fw-semibold text-decoration-none">{{ $p->barang->nama }}</a><div class="small text-muted">{{ $p->barang->kode }} · {{ $p->barang->ruangan->nama }}</div></td>
                <td style="max-width:220px">{{ $p->deskripsi_kerusakan }}</td>
                <td style="max-width:220px">{{ $p->rincian_perbaikan ?: '-' }}</td>
                <td>{{ $p->penanganan }}</td>
                <td class="text-end text-nowrap">{{ $p->biaya ? \App\Support\Fmt::rp($p->biaya) : '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $perbaikan->links() }}</div>
@endsection
