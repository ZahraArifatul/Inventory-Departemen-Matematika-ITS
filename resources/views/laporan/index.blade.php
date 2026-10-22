@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
@php $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
<h4 class="mb-1">Laporan &amp; Ekspor Data</h4>
<p class="text-muted">Ekspor menghasilkan file CSV yang langsung terbuka di Excel. Untuk PDF, buka halaman terkait lalu klik <b>Cetak / PDF</b> dan pilih "Save as PDF".</p>

<div class="row g-3">
    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <h6><i class="bi bi-box-seam"></i> Daftar Barang</h6>
        <form method="get" action="{{ route('laporan.export', 'barang') }}" class="d-flex gap-2">
            <select name="kondisi" class="form-select"><option value="">Semua kondisi</option>@foreach (\App\Models\Barang::KONDISI as $kode => $label)<option value="{{ $kode }}">{{ $label }}</option>@endforeach</select>
            <button class="btn btn-success text-nowrap"><i class="bi bi-file-earmark-excel"></i> Unduh</button>
        </form>
        <a href="{{ route('barang.index') }}" class="small">Buka halaman untuk cetak / PDF</a>
    </div></div></div>

    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <h6><i class="bi bi-graph-up"></i> Analisis Kelayakan</h6>
        <p class="small text-muted mb-2">Seluruh barang aktif lengkap dengan skor dan rekomendasi.</p>
        <a href="{{ route('laporan.export', 'analisis') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Unduh</a>
        <a href="{{ route('analisis.index') }}" class="btn btn-link">Cetak / PDF</a>
    </div></div></div>

    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <h6><i class="bi bi-wrench-adjustable"></i> Riwayat Perbaikan</h6>
        <form method="get" action="{{ route('laporan.export', 'perbaikan') }}" class="d-flex gap-2">
            <select name="bulan" class="form-select"><option value="">Semua bulan</option>@foreach ($namaBulan as $no => $nm)<option value="{{ $no }}">{{ $nm }}</option>@endforeach</select>
            <select name="tahun" class="form-select"><option value="">Semua tahun</option>@foreach ($tahunList as $th)<option>{{ $th }}</option>@endforeach</select>
            <button class="btn btn-success text-nowrap"><i class="bi bi-file-earmark-excel"></i> Unduh</button>
        </form>
        <a href="{{ route('perbaikan.index') }}" class="small">Buka halaman untuk cetak / PDF</a>
    </div></div></div>

    <div class="col-md-6"><div class="card h-100"><div class="card-body">
        <h6><i class="bi bi-arrow-left-right"></i> Riwayat Perpindahan</h6>
        <form method="get" action="{{ route('laporan.export', 'mutasi') }}" class="d-flex gap-2">
            <select name="bulan" class="form-select"><option value="">Semua bulan</option>@foreach ($namaBulan as $no => $nm)<option value="{{ $no }}">{{ $nm }}</option>@endforeach</select>
            <select name="tahun" class="form-select"><option value="">Semua tahun</option>@foreach ($tahunList as $th)<option>{{ $th }}</option>@endforeach</select>
            <button class="btn btn-success text-nowrap"><i class="bi bi-file-earmark-excel"></i> Unduh</button>
        </form>
        <a href="{{ route('mutasi.index') }}" class="small">Buka halaman untuk cetak / PDF</a>
    </div></div></div>
</div>
@endsection
