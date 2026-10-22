@extends('layouts.app')
@section('title', $barang->nama)
@section('content')
@php $u = auth()->user(); $aktif = $barang->kondisi !== 'dihapus'; @endphp

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-1">{{ $barang->nama }}</h4>
        <span class="text-muted">{{ $barang->kode }} · {{ $barang->kategori->nama }}</span>
    </div>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer"></i></button>
        @if ($u->bisaKelola())<a href="{{ route('barang.edit', $barang) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>@endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3"><div class="card-body">
            <div class="mb-3"><span class="badge text-bg-{{ $barang->warna_kondisi }} fs-6">{{ $barang->label_kondisi }}</span></div>
            <dl class="row mb-0">
                <dt class="col-5">Ruangan sekarang</dt><dd class="col-7">{{ $barang->ruangan->nama }}</dd>
                <dt class="col-5">Spesifikasi</dt><dd class="col-7">{{ $barang->spesifikasi ?: '-' }}</dd>
                <dt class="col-5">Tgl pengadaan</dt><dd class="col-7">{{ \App\Support\Fmt::tgl($barang->tanggal_pengadaan, 'd F Y') }}</dd>
                <dt class="col-5">Usia</dt><dd class="col-7">{{ number_format($barang->usia_tahun, 1, ',', '.') }} tahun @if($barang->kategori->umur_ekonomis_tahun)<span class="text-muted small">(umur ekonomis {{ $barang->kategori->umur_ekonomis_tahun }} th)</span>@endif</dd>
                <dt class="col-5">Nilai awal</dt><dd class="col-7">{{ \App\Support\Fmt::rp($barang->nilai_awal) }}</dd>
                <dt class="col-5">Jumlah perbaikan</dt><dd class="col-7">{{ $barang->perbaikan_count }}x <span class="text-muted small">({{ $barang->perbaikan_setahun_count }}x dalam 12 bulan)</span></dd>
                <dt class="col-5">Total biaya perbaikan</dt><dd class="col-7">{{ \App\Support\Fmt::rp($barang->perbaikan_sum_biaya ?? 0) }} <span class="text-muted small">({{ \App\Support\Fmt::persen($nilai['rasio']) }} dari nilai awal)</span></dd>
                @if ($barang->catatan)<dt class="col-5">Catatan</dt><dd class="col-7">{{ $barang->catatan }}</dd>@endif
            </dl>
        </div></div>

        <div class="card mb-3 border-start border-4 border-{{ $nilai['warna'] }}"><div class="card-body">
            <div class="text-muted small">Analisis kelayakan</div>
            <div class="fs-5 fw-semibold text-{{ $nilai['warna'] }}">{{ $nilai['label'] }}</div>
            @if ($nilai['alasan'])
                <ul class="small mb-0 mt-2">@foreach ($nilai['alasan'] as $a)<li>{{ $a }}</li>@endforeach</ul>
            @elseif ($aktif)
                <div class="small text-muted mt-1">Belum ada indikator masalah.</div>
            @endif
            <div class="small text-muted mt-2">Rekomendasi berupa saran dari data; keputusan akhir tetap di pihak manajemen.</div>
        </div></div>

        @if ($u->bisaKelola() && $aktif)
        <div class="card mb-3 no-print"><div class="card-header bg-white fw-semibold">Ubah Kondisi</div><div class="card-body">
            <form method="post" action="{{ route('barang.kondisi', $barang) }}" class="d-flex gap-2"
                  onsubmit="if(this.kondisi.value==='dihapus'){return confirm('Tandai barang ini sebagai DIHAPUS dari inventaris?')}">
                @csrf @method('PATCH')
                <select name="kondisi" class="form-select">
                    @foreach (\App\Models\Barang::KONDISI as $kode => $label)<option value="{{ $kode }}" @selected($barang->kondisi === $kode)>{{ $label }}</option>@endforeach
                </select>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div></div>

        <div class="card mb-3 no-print"><div class="card-header bg-white fw-semibold">Pindahkan ke Ruangan Lain</div><div class="card-body">
            <form method="post" action="{{ route('barang.mutasi.store', $barang) }}">
                @csrf
                <div class="mb-2"><label class="form-label">Ruangan tujuan *</label>
                    <select name="ruangan_tujuan_id" class="form-select" required><option value="">Pilih...</option>
                        @foreach ($daftarRuangan as $r)@if ($r->id != $barang->ruangan_id)<option value="{{ $r->id }}" @selected(old('ruangan_tujuan_id') == $r->id)>{{ $r->nama }}</option>@endif @endforeach
                    </select></div>
                <div class="mb-2"><label class="form-label">Tanggal pindah *</label><input type="date" name="tanggal_pindah" class="form-control" value="{{ old('tanggal_pindah', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required></div>
                <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="{{ old('keterangan') }}"></div>
                <button class="btn btn-outline-primary w-100"><i class="bi bi-arrow-left-right"></i> Pindahkan</button>
            </form>
        </div></div>

        <div class="card no-print"><div class="card-header bg-white fw-semibold">Catat Perbaikan</div><div class="card-body">
            <form method="post" action="{{ route('barang.perbaikan.store', $barang) }}">
                @csrf
                @include('perbaikan._fields', ['tampilKondisi' => true, 'barangSaatIni' => $barang])
                <button class="btn btn-success w-100 mt-3"><i class="bi bi-check2-circle"></i> Simpan Perbaikan</button>
            </form>
        </div></div>
        @endif
    </div>

    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Riwayat Perpindahan Ruangan</div>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Asal</th><th>Tujuan</th><th>Masuk</th><th>Keluar</th><th>Petugas</th></tr></thead>
                <tbody>
                @foreach ($barang->mutasi as $m)
                    <tr>
                        <td>{{ $m->asal?->nama ?? '-' }}</td>
                        <td class="fw-semibold">{{ $m->tujuan->nama }}@if($m->keterangan)<div class="small text-muted fw-normal">{{ $m->keterangan }}</div>@endif</td>
                        <td class="text-nowrap">{{ \App\Support\Fmt::tgl($m->tanggal_masuk) }}</td>
                        <td class="text-nowrap">{!! $m->tanggal_keluar ? e(\App\Support\Fmt::tgl($m->tanggal_keluar)) : '<span class="badge text-bg-primary">Masih di sini</span>' !!}</td>
                        <td>{{ $m->petugas?->name ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Riwayat Perbaikan</div>
            <div class="table-responsive"><table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kerusakan &amp; Perbaikan</th><th>Penanganan</th><th class="text-end">Biaya</th><th class="no-print"></th></tr></thead>
                <tbody>
                @forelse ($barang->perbaikan as $p)
                    <tr>
                        <td class="text-nowrap">{{ \App\Support\Fmt::tgl($p->tanggal_perbaikan) }}</td>
                        <td>
                            <div><span class="text-danger small">Rusak:</span> {{ $p->deskripsi_kerusakan }}</div>
                            @if ($p->rincian_perbaikan)<div class="small text-muted">Perbaikan: {{ $p->rincian_perbaikan }}</div>@endif
                        </td>
                        <td>{{ $p->penanganan }}<div class="small text-muted">dicatat: {{ $p->petugas?->name ?? '-' }}</div></td>
                        <td class="text-end text-nowrap">{{ $p->biaya ? \App\Support\Fmt::rp($p->biaya) : '-' }}</td>
                        <td class="text-end no-print">@if ($u->bisaKelola())<a href="{{ route('perbaikan.edit', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat perbaikan.</td></tr>
                @endforelse
                </tbody>
                @if ($barang->perbaikan->count())
                <tfoot class="table-light"><tr><th colspan="3" class="text-end">Total biaya perbaikan</th><th class="text-end">{{ \App\Support\Fmt::rp($barang->perbaikan_sum_biaya ?? 0) }}</th><th class="no-print"></th></tr></tfoot>
                @endif
            </table></div>
        </div>
    </div>
</div>
@endsection
