<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Inventaris Departemen Matematika ITS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>body{background:#f5f7fa}</style>
</head>
<body>
<div class="container" style="max-width:420px;margin-top:10vh">
    <div class="text-center mb-4">
        <i class="bi bi-box-seam text-primary" style="font-size:3rem"></i>
        <h4 class="mt-2 mb-0">Sistem Inventaris Barang</h4>
        <div class="text-muted">Departemen Matematika ITS</div>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        @if ($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('login.proses') }}">
            @csrf
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="ingat" id="ingat" value="1"><label for="ingat" class="form-check-label">Ingat saya</label></div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
    </div></div>
</div>
</body>
</html>
