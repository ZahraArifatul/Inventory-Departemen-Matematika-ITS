@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h4 class="mb-3">Dashboard Monitoring</h4>

<div class="row g-3 mb-3">
    @foreach (\App\Models\Barang::KONDISI as $kode => $label)
        <div class="col-6 col-md-3">
            <a href="{{ route('barang.index', ['kondisi' => $kode]) }}" class="text-decoration-none">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-2 fw-bold text-{{ \App\Models\Barang::WARNA[$kode] }}">{{ $perKondisi[$kode] ?? 0 }}</div>
                </div></div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card"><div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="text-muted small">Total biaya perbaikan tahun {{ now()->year }}</div><div class="fs-4 fw-bold">{{ \App\Support\Fmt::rp($biayaTahunIni) }}</div></div>
        <i class="bi bi-wrench-adjustable fs-1 text-primary opacity-50"></i>
    </div></div></div>
    <div class="col-md-6"><a href="{{ route('analisis.index', ['rekomendasi' => 'ganti']) }}" class="text-decoration-none"><div class="card"><div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="text-muted small">Disarankan dihapus &amp; dibeli baru</div><div class="fs-4 fw-bold text-danger">{{ $disarankanGanti }} barang</div></div>
        <i class="bi bi-arrow-repeat fs-1 text-danger opacity-50"></i>
    </div></div></a></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100"><div class="card-header bg-white fw-semibold">Jumlah Barang per Ruangan</div>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <tbody>
                @forelse ($perRuangan as $r)
                    <tr><td><a href="{{ route('barang.index', ['ruangan_id' => $r->id]) }}" class="text-decoration-none">{{ $r->nama }}</a></td><td class="text-end fw-semibold">{{ $r->jumlah_barang }}</td></tr>
                @empty
                    <tr><td class="text-muted text-center py-3">Belum ada ruangan.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100"><div class="card-header bg-white fw-semibold">Barang yang Rusak</div>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <tbody>
                @forelse ($perhatian as $b)
                    <tr>
                        <td><a href="{{ route('barang.show', $b) }}" class="text-decoration-none fw-semibold">{{ $b->nama }}</a><div class="small text-muted">{{ $b->kode }} · {{ $b->ruangan->nama }}</div></td>
                        <td class="text-end"><span class="badge text-bg-{{ $b->warna_kondisi }}">{{ $b->label_kondisi }}</span></td>
                    </tr>
                @empty
                    <tr><td class="text-muted text-center py-3">Tidak ada barang yang rusak.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card"><div class="card-header bg-white fw-semibold">Perbaikan Terbaru</div>
            <ul class="list-group list-group-flush">
            @forelse ($perbaikanTerbaru as $p)
                <li class="list-group-item d-flex justify-content-between">
                    <span><a href="{{ route('barang.show', $p->barang_id) }}" class="text-decoration-none">{{ $p->barang->nama }}</a><div class="small text-muted">{{ \Illuminate\Support\Str::limit($p->deskripsi_kerusakan, 50) }}</div></span>
                    <span class="text-end small text-muted">{{ \App\Support\Fmt::tgl($p->tanggal_perbaikan) }}<br>{{ \App\Support\Fmt::rp($p->biaya) }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada data.</li>
            @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-header bg-white fw-semibold">Perpindahan Terbaru</div>
            <ul class="list-group list-group-flush">
            @forelse ($mutasiTerbaru as $m)
                <li class="list-group-item d-flex justify-content-between">
                    <span><a href="{{ route('barang.show', $m->barang_id) }}" class="text-decoration-none">{{ $m->barang->nama }}</a><div class="small text-muted">{{ $m->asal?->nama ?? 'Penempatan awal' }} → {{ $m->tujuan->nama }}</div></span>
                    <span class="small text-muted">{{ \App\Support\Fmt::tgl($m->tanggal_masuk) }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada data.</li>
            @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
