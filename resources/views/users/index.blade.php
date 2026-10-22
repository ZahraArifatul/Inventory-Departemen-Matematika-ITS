@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manajemen Pengguna</h4>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pengguna</a>
</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
    <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @foreach ($users as $x)
        <tr>
            <td>{{ $x->name }}</td><td>{{ $x->email }}</td>
            <td><span class="badge text-bg-primary">{{ $x->label_role }}</span></td>
            <td>{!! $x->aktif ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' !!}</td>
            <td class="text-end text-nowrap"><div class="d-flex gap-1 justify-content-end">
                <a href="{{ route('users.edit', $x) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                @if ($x->id !== auth()->id())
                <form method="post" action="{{ route('users.destroy', $x) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                @endif
            </div></td>
        </tr>
    @endforeach
    </tbody>
</table></div></div>
@endsection
