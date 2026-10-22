@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h4 class="mb-3">Dashboard</h4>

<div class="row g-3 mb-4">
    @foreach ([['Total Aset Aktif', $stat['total'], 'primary', 'box-seam'], ['Terlambat Servis', $stat['terlambat'], 'danger', 'exclamation-octagon'], ['Segera Servis (≤30 hari)', $stat['segera'], 'warning', 'clock-history'], ['Aman', $stat['aman'], 'success', 'check-circle']] as [$label, $val, $warna, $ikon])
        <div class="col-6 col-md-3">
            <div class="card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small">{{ $label }}</div><div class="fs-2 fw-bold text-{{ $warna }}">{{ $val }}</div></div>
                <i class="bi bi-{{ $ikon }} fs-1 text-{{ $warna }} opacity-50"></i>
            </div></div>
        </div>
    @endforeach
</div>

@php $tabel = [['Perlu Segera Diservis (Terlambat)', $terlambat, 'danger'], ['Akan Jatuh Tempo', $segera, 'warning']]; @endphp
@foreach ($tabel as [$judul, $data, $warna])
<div class="card mb-4">
    <div class="card-header bg-white fw-semibold text-{{ $warna }}">{{ $judul }}</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode</th><th>Nama</th><th>Lokasi</th><th>Terakhir Servis</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($data as $a)
                <tr>
                    <td>{{ $a->kode }}</td>
                    <td><a href="{{ route('assets.show', $a) }}" class="fw-semibold text-decoration-none">{{ $a->nama }}</a><div class="small text-muted">{{ $a->category->nama }}</div></td>
                    <td>{{ $a->lokasi ?? '-' }}</td>
                    <td>{{ $a->terakhir_servis?->translatedFormat('d M Y') ?? 'Belum pernah' }}</td>
                    <td>{{ $a->jatuh_tempo->translatedFormat('d M Y') }}</td>
                    <td><span class="badge text-bg-{{ $a->label_status_servis[1] }}">{{ $a->label_status_servis[0] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach

<div class="card">
    <div class="card-header bg-white fw-semibold">Servis Terakhir Dicatat</div>
    <ul class="list-group list-group-flush">
        @forelse ($riwayat as $m)
            <li class="list-group-item d-flex justify-content-between">
                <span><a href="{{ route('assets.show', $m->asset) }}" class="text-decoration-none">{{ $m->asset->nama }}</a> — {{ $m->jenis }}</span>
                <span class="text-muted">{{ $m->tanggal_servis->translatedFormat('d M Y') }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">Belum ada riwayat servis.</li>
        @endforelse
    </ul>
</div>
@endsection
