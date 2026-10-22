<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventaris') - Inventaris Departemen Matematika ITS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{background:#f5f7fa}
        .card{border:0;box-shadow:0 1px 3px rgba(0,0,0,.08)}
        @media print{
            .no-print,.navbar,.pagination,.alert{display:none!important}
            body{background:#fff}
            .card{box-shadow:none;border:1px solid #ddd}
            .container{max-width:100%!important}
        }
    </style>
</head>
<body>
@php $u = auth()->user(); @endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}"><i class="bi bi-box-seam"></i> Inventaris Matematika ITS</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}">Barang</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('mutasi.*') ? 'active' : '' }}" href="{{ route('mutasi.index') }}">Perpindahan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('perbaikan.*') ? 'active' : '' }}" href="{{ route('perbaikan.index') }}">Perbaikan</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('analisis.*') ? 'active' : '' }}" href="{{ route('analisis.index') }}">Analisis Kelayakan</a></li>
                @if ($u->bisaLaporan())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">Laporan</a></li>
                @endif
                @if ($u->isAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Master Data</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('ruangan.index') }}">Ruangan</a></li>
                            <li><a class="dropdown-item" href="{{ route('kategori.index') }}">Kategori Barang</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}">Pengguna</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center text-white gap-2">
                <span class="small"><i class="bi bi-person-circle"></i> {{ $u->name }} <span class="badge text-bg-light">{{ $u->label_role }}</span></span>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Keluar</button></form>
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('ok') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
