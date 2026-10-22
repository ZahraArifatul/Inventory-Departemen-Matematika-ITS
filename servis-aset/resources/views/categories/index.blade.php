@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<h4 class="mb-3">Kategori Aset</h4>
<div class="row g-3">
    <div class="col-md-5">
        <div class="card"><div class="card-header bg-white fw-semibold">Tambah Kategori</div><div class="card-body">
            <form method="post" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-2"><label class="form-label">Nama *</label><input name="nama" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Interval servis default (bulan)</label><input type="number" min="1" name="interval_default_bulan" class="form-control"></div>
                <button class="btn btn-primary">Tambah</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-7">
        <div class="card"><table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Interval default</th><th>Jumlah aset</th><th></th></tr></thead>
            <tbody>
            @foreach ($categories as $c)
                <tr>
                    <td>{{ $c->nama }}</td>
                    <td>{{ $c->interval_default_bulan ? $c->interval_default_bulan . ' bulan' : '-' }}</td>
                    <td>{{ $c->assets_count }}</td>
                    <td class="text-end">
                        <form method="post" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
</div>
@endsection
