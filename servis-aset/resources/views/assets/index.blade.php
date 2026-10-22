@extends('layouts.app')
@section('title', 'Daftar Aset')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Aset</h4>
    <a href="{{ route('assets.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Aset</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-5"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / kode / lokasi / merk"></div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->nama }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode</th><th>Nama</th><th>Lokasi</th><th>Terakhir Servis</th><th>Servis Berikutnya</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($assets as $a)
                <tr class="{{ $a->status === 'nonaktif' ? 'text-muted' : '' }}">
                    <td>{{ $a->kode }}</td>
                    <td><a href="{{ route('assets.show', $a) }}" class="fw-semibold text-decoration-none">{{ $a->nama }}</a>
                        <div class="small text-muted">{{ $a->category->nama }} @if($a->merk) · {{ $a->merk }} @endif</div></td>
                    <td>{{ $a->lokasi ?? '-' }}</td>
                    <td>{{ $a->terakhir_servis?->translatedFormat('d M Y') ?? 'Belum pernah' }}</td>
                    <td>{{ $a->jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}</td>
                    <td>
                        @if ($a->status === 'nonaktif') <span class="badge text-bg-dark">Nonaktif</span>
                        @else <span class="badge text-bg-{{ $a->label_status_servis[1] }}">{{ $a->label_status_servis[0] }}</span> @endif
                    </td>
                    <td class="text-end"><a href="{{ route('assets.edit', $a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada aset.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $assets->links() }}</div>
@endsection
