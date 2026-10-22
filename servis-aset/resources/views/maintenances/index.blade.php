@extends('layouts.app')
@section('title', 'Riwayat Servis')
@section('content')
<h4 class="mb-3">Semua Riwayat Servis</h4>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th>Aset</th><th>Jenis</th><th>Teknisi</th><th class="text-end">Biaya</th></tr></thead>
            <tbody>
            @forelse ($maintenances as $m)
                <tr>
                    <td>{{ $m->tanggal_servis->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ route('assets.show', $m->asset) }}" class="text-decoration-none fw-semibold">{{ $m->asset->nama }}</a><div class="small text-muted">{{ $m->asset->kode }} · {{ $m->asset->category->nama }}</div></td>
                    <td>{{ $m->jenis }}</td>
                    <td>{{ $m->teknisi ?? '-' }}</td>
                    <td class="text-end">{{ $m->biaya ? 'Rp ' . number_format($m->biaya, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $maintenances->links() }}</div>
@endsection
