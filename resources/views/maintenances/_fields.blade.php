@php
    $m = $maintenance ?? new \App\Models\Maintenance();
    $tanggal = old('tanggal_servis', ($m->tanggal_servis ?? now())->format('Y-m-d'));
    $jenisDipilih = old('jenis', $m->jenis ?: 'Servis rutin');
    $daftarJenis = \App\Models\Maintenance::JENIS;
    if (!in_array($jenisDipilih, $daftarJenis)) { $daftarJenis[] = $jenisDipilih; }
@endphp
<div class="mb-2"><label class="form-label">Tanggal servis *</label>
    <input type="date" name="tanggal_servis" class="form-control" value="{{ $tanggal }}" max="{{ now()->format('Y-m-d') }}" required></div>
<div class="mb-2"><label class="form-label">Jenis *</label>
    <select name="jenis" class="form-select">@foreach ($daftarJenis as $j)<option @selected($jenisDipilih === $j)>{{ $j }}</option>@endforeach</select></div>
<div class="mb-2"><label class="form-label">Kerusakan / Keluhan</label>
    <textarea name="kerusakan" rows="2" class="form-control" placeholder="mis. AC tidak dingin, freon bocor">{{ old('kerusakan', $m->kerusakan) }}</textarea></div>
<div class="mb-2"><label class="form-label">Tindakan / Keterangan</label>
    <textarea name="keterangan" rows="2" class="form-control" placeholder="mis. isi freon, ganti kapasitor">{{ old('keterangan', $m->keterangan) }}</textarea></div>
<div class="mb-2"><label class="form-label">Teknisi / Vendor</label>
    <input name="teknisi" class="form-control" value="{{ old('teknisi', $m->teknisi) }}"></div>
<div class="mb-2"><label class="form-label">Biaya (Rp)</label>
    <input type="number" min="0" name="biaya" class="form-control" value="{{ old('biaya', $m->biaya ?: '') }}"></div>
