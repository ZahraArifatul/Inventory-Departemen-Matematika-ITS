@extends('layouts.app')
@section('title', 'Riwayat Servis')
@section('content')
@php
    $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $adaFilter = request()->hasAny(['q','bulan','tahun','jenis','category_id']) && collect(request()->only(['q','bulan','tahun','jenis','category_id']))->filter()->isNotEmpty();
@endphp
<h4 class="mb-3">Riwayat Servis &amp; Kerusakan</h4>

<form class="card card-body mb-3" method="get">
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <select name="bulan" class="form-select">
                <option value="">Semua bulan</option>
                @foreach ($namaBulan as $no => $nm)<option value="{{ $no }}" @selected(request('bulan') == $no)>{{ $nm }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="tahun" class="form-select">
                <option value="">Semua tahun</option>
                @foreach ($tahunList as $th)<option @selected(request('tahun') == $th)>{{ $th }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="category_id" class="form-select">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->nama }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="jenis" class="form-select">
                <option value="">Semua jenis</option>
                @foreach ($jenisList as $j)<option @selected(request('jenis') === $j)>{{ $j }}</option>@endforeach
            </select>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari aset / kerusakan / teknisi">
            <button class="btn btn-primary"><i class="bi bi-funnel"></i></button>
            @if ($adaFilter)<a href="{{ route('maintenances.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>@endif
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Jumlah servis</div><div class="fs-3 fw-bold">{{ $jumlah }}</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Total biaya</div><div class="fs-3 fw-bold">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div></div></div></div>
    <div class="col-12 col-md-6">
        <div class="card h-100"><div class="card-body py-2">
            <div class="text-muted small mb-1">Aset paling sering diservis{{ $adaFilter ? ' (sesuai filter)' : '' }}</div>
            @forelse ($teratas as $t)
                <div class="d-flex justify-content-between small">
                    <a href="{{ route('assets.show', $t->asset_id) }}" class="text-decoration-none">{{ $t->asset->nama }}</a>
                    <span>{{ $t->jumlah }}x · Rp {{ number_format($t->total_biaya, 0, ',', '.') }}</span>
                </div>
            @empty
                <div class="small text-muted">-</div>
            @endforelse
        </div></div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Aset</th><th>Jenis</th><th>Kerusakan</th><th>Tindakan</th><th>Teknisi</th><th class="text-end">Biaya</th><th></th></tr></thead>
            <tbody>
            @forelse ($maintenances as $m)
                <tr>
                    <td class="text-nowrap">{{ $m->tanggal_servis->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ route('assets.show', $m->asset) }}" class="text-decoration-none fw-semibold">{{ $m->asset->nama }}</a><div class="small text-muted">{{ $m->asset->kode }} · {{ $m->asset->category->nama }}</div></td>
                    <td>{{ $m->jenis }}</td>
                    <td style="max-width:220px">{{ $m->kerusakan ?? '-' }}</td>
                    <td style="max-width:220px">{{ $m->keterangan ?? '-' }}</td>
                    <td>{{ $m->teknisi ?? '-' }}</td>
                    <td class="text-end text-nowrap">{{ $m->biaya ? 'Rp ' . number_format($m->biaya, 0, ',', '.') : '-' }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('maintenances.edit', $m) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $maintenances->links() }}</div>
@endsection
