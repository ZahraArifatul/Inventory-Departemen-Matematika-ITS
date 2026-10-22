@php $baru = !$user->exists; @endphp
<div class="mb-2"><label class="form-label">Nama *</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
<div class="mb-2"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
<div class="mb-2"><label class="form-label">Peran *</label>
    <select name="role" class="form-select">@foreach (\App\Models\User::ROLES as $kode => $label)<option value="{{ $kode }}" @selected(old('role', $user->role) === $kode)>{{ $label }}</option>@endforeach</select></div>
<div class="mb-2"><label class="form-label">Password {{ $baru ? '*' : '(kosongkan jika tidak diubah)' }}</label><input type="password" name="password" class="form-control" {{ $baru ? 'required' : '' }} minlength="8" autocomplete="new-password"></div>
<div class="mb-3"><label class="form-label">Ulangi password</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
<div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="aktif" id="aktif" value="1" @checked(old('aktif', $user->aktif ?? true))><label class="form-check-label" for="aktif">Akun aktif</label></div>
