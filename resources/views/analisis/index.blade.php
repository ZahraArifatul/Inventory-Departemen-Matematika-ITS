@extends('layouts.app')
@section('title', 'Analisis Kelayakan')
@section('content')
@php $u = auth()->user(); @endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Analisis Kelayakan Barang</h4>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i> Cetak / PDF</button>
        @if ($u->bisaLaporan())<a href="{{ route('laporan.export', 'analisis') }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel"></i> Ekspor Excel</a>@endif
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach ([['ganti', 'Disarankan dihapus & dibeli baru', 'danger'], ['pantau', 'Perlu dipantau', 'warning'], ['layak', 'Masih layak diperbaiki', 'success']] as [$kode, $label, $warna])
        <div class="col-md-4"><a href="{{ route('analisis.index', ['rekomendasi' => $kode]) }}" class="text-decoration-none"><div class="card"><div class="card-body">
            <div class="text-muted small">{{ $label }}</div><div class="fs-2 fw-bold text-{{ $warna }}">{{ $ringkasan[$kode] }}</div>
        </div></div></a></div>
    @endforeach
</div>

<form class="row g-2 mb-3 no-print" method="get">
    <div class="col-md-3"><select name="rekomendasi" class="form-select"><option value="">Semua rekomendasi</option>@foreach (\App\Services\Kelayakan::LABEL as $kode => $l)@if ($kode !== 'dihapus')<option value="{{ $kode }}" @selected(request('rekomendasi') === $kode)>{{ $l[0] }}</option>@endif @endforeach</select></div>
    <div class="col-md-3"><select name="kategori_id" class="form-select"><option value="">Semua kategori</option>@foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></div>
    <div class="col-md-3"><select name="ruangan_id" class="form-select"><option value="">Semua ruangan</option>@foreach ($daftarRuangan as $r)<option value="{{ $r->id }}" @selected(request('ruangan_id') == $r->id)>{{ $r->nama }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a></div>
</form>

<div class="card mb-3"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Barang</th><th>Usia</th><th class="text-center">Perbaikan<br><span class="small fw-normal">total / 12 bln</span></th><th class="text-end">Total Biaya</th><th class="text-end">% Nilai Awal</th><th>Rekomendasi</th></tr></thead>
        <tbody>
        @forelse ($hasil as $r)
            @php $b = $r['barang']; $n = $r['n']; @endphp
            <tr>
                <td><a href="{{ route('barang.show', $b) }}" class="fw-semibold text-decoration-none">{{ $b->nama }}</a><div class="small text-muted">{{ $b->kode }} · {{ $b->kategori->nama }} · {{ $b->ruangan->nama }}</div></td>
                <td class="text-nowrap">{{ number_format($n['usia'], 1, ',', '.') }} th @if($n['umur'])<div class="small text-muted">dari {{ $n['umur'] }} th</div>@endif</td>
                <td class="text-center">{{ $n['frekuensi'] }}x / {{ $n['frek_tahun'] }}x</td>
                <td class="text-end text-nowrap">{{ \App\Support\Fmt::rp($n['total_biaya']) }}</td>
                <td class="text-end">{{ \App\Support\Fmt::persen($n['rasio']) }}</td>
                <td>
                    <span class="badge text-bg-{{ $n['warna'] }}">{{ $n['label'] }}</span>
                    @if ($n['alasan'])<ul class="small text-muted mb-0 ps-3 mt-1">@foreach ($n['alasan'] as $a)<li>{{ $a }}</li>@endforeach</ul>@endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>

<div class="card"><div class="card-body small">
    <div class="fw-semibold mb-1">Dasar penilaian</div>
    Setiap barang diberi skor dari empat indikator. Skor 5 atau lebih = disarankan dihapus &amp; dibeli baru, skor 3-4 = perlu dipantau, di bawah 3 = masih layak diperbaiki.
    <ul class="mb-1 mt-1">
        <li>Total biaya perbaikan dibanding nilai awal: &ge;30% (+1), &ge;50% (+2), &ge;75% (+3)</li>
        <li>Frekuensi perbaikan 12 bulan terakhir: &ge;2x (+1), &ge;3x (+2), &ge;5x (+3)</li>
        <li>Usia dibanding umur ekonomis kategori: &ge;75% (+1), &ge;100% (+2)</li>
        <li>Kondisi saat ini Rusak Berat: +2</li>
    </ul>
    Ini saran berbasis data untuk membantu manajemen, bukan keputusan otomatis.
</div></div>
@endsection
