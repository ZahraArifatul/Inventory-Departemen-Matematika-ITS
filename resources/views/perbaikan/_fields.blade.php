@php
    $p = $perbaikan ?? new \App\Models\Perbaikan();
    $tanggal = old('tanggal_perbaikan', ($p->tanggal_perbaikan ?? now())->format('Y-m-d'));
@endphp
<div class="mb-2"><label class="form-label">Tanggal perbaikan *</label>
    <input type="date" name="tanggal_perbaikan" class="form-control" value="{{ $tanggal }}" max="{{ now()->format('Y-m-d') }}" required></div>
<div class="mb-2"><label class="form-label">Deskripsi kerusakan *</label>
    <textarea name="deskripsi_kerusakan" rows="2" class="form-control" required placeholder="mis. layar tidak menyala">{{ old('deskripsi_kerusakan', $p->deskripsi_kerusakan) }}</textarea></div>
<div class="mb-2"><label class="form-label">Rincian perbaikan yang dilakukan</label>
    <textarea name="rincian_perbaikan" rows="2" class="form-control" placeholder="mis. ganti kabel fleksibel layar">{{ old('rincian_perbaikan', $p->rincian_perbaikan) }}</textarea></div>
<div class="mb-2"><label class="form-label">Teknisi / vendor yang menangani *</label>
    <input name="penanganan" class="form-control" value="{{ old('penanganan', $p->penanganan ?: auth()->user()->name) }}" required></div>
<div class="mb-2"><label class="form-label">Biaya perbaikan (Rp)</label>
    <input type="number" min="0" name="biaya" class="form-control" value="{{ old('biaya', $p->biaya ?: '') }}"></div>
@if (!empty($tampilKondisi))
<div class="mb-2"><label class="form-label">Kondisi barang setelah perbaikan</label>
    <select name="kondisi_setelah" class="form-select">
        <option value="">Tidak diubah (sekarang: {{ $barangSaatIni->label_kondisi }})</option>
        @foreach (\App\Models\Barang::KONDISI as $kode => $label)<option value="{{ $kode }}" @selected(old('kondisi_setelah') === $kode)>{{ $label }}</option>@endforeach
    </select></div>
@endif
