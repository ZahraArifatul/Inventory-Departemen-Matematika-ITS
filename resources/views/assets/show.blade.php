@extends('layouts.app')
@section('title', $asset->nama)
@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-1">{{ $asset->nama }}</h4>
        <span class="text-muted">{{ $asset->kode }} · {{ $asset->category->nama }}</span>
    </div>
    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3"><div class="card-body">
            <div class="mb-3">
                @if ($asset->status === 'nonaktif') <span class="badge text-bg-dark fs-6">Nonaktif</span>
                @else <span class="badge text-bg-{{ $asset->label_status_servis[1] }} fs-6">{{ $asset->label_status_servis[0] }}</span> @endif
            </div>
            <dl class="row mb-0">
                <dt class="col-5">Jumlah servis</dt><dd class="col-7">{{ $asset->maintenances->count() }}x</dd>
                <dt class="col-5">Terakhir servis</dt><dd class="col-7">{{ $asset->terakhir_servis?->translatedFormat('d F Y') ?? 'Belum pernah' }}</dd>
                <dt class="col-5">Interval</dt><dd class="col-7">{{ $asset->interval_servis_bulan ? $asset->interval_servis_bulan . ' bulan' : '-' }}</dd>
                <dt class="col-5">Servis berikutnya</dt><dd class="col-7">{{ $asset->jatuh_tempo?->translatedFormat('d F Y') ?? '-' }}</dd>
                <dt class="col-5">Lokasi</dt><dd class="col-7">{{ $asset->lokasi ?? '-' }}</dd>
                <dt class="col-5">Merk / Model</dt><dd class="col-7">{{ $asset->merk ?? '-' }}</dd>
                <dt class="col-5">Serial number</dt><dd class="col-7">{{ $asset->serial_number ?? '-' }}</dd>
                <dt class="col-5">Tanggal beli</dt><dd class="col-7">{{ $asset->tanggal_beli?->translatedFormat('d F Y') ?? '-' }}</dd>
                <dt class="col-5">Total biaya servis</dt><dd class="col-7">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</dd>
                @if ($asset->catatan)<dt class="col-5">Catatan</dt><dd class="col-7">{{ $asset->catatan }}</dd>@endif
            </dl>
        </div></div>

        <div class="card"><div class="card-header bg-white fw-semibold">Catat Servis / Kerusakan Baru</div><div class="card-body">
            <form method="post" action="{{ route('assets.maintenances.store', $asset) }}">
                @csrf
                @include('maintenances._fields')
                <button class="btn btn-success w-100 mt-3"><i class="bi bi-check2-circle"></i> Simpan</button>
            </form>
        </div></div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Riwayat Servis</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Tanggal</th><th>Jenis &amp; Kerusakan</th><th>Teknisi</th><th class="text-end">Biaya</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($asset->maintenances as $m)
                        <tr>
                            <td class="text-nowrap">{{ $m->tanggal_servis->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $m->jenis }}</div>
                                @if($m->kerusakan)<div class="small"><span class="text-danger">Rusak:</span> {{ $m->kerusakan }}</div>@endif
                                @if($m->keterangan)<div class="small text-muted">Tindakan: {{ $m->keterangan }}</div>@endif
                            </td>
                            <td>{{ $m->teknisi ?? '-' }}</td>
                            <td class="text-end text-nowrap">{{ $m->biaya ? 'Rp ' . number_format($m->biaya, 0, ',', '.') : '-' }}</td>
                            <td class="text-end text-nowrap">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('maintenances.edit', $m) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="{{ route('maintenances.destroy', $m) }}" onsubmit="return confirm('Hapus riwayat ini?')">
                                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat servis.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
