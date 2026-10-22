@php
    $v = fn ($f) => old($f, $barang->{$f} ?? '');
    $tgl = old('tanggal_pengadaan', $barang->tanggal_pengadaan?->format('Y-m-d'));
    $buat = !$barang->exists;
@endphp
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">Kode / ID Barang *</label><input name="kode" class="form-control" value="{{ $v('kode') }}" required placeholder="mis. INV-001"></div>
    <div class="col-md-8"><label class="form-label">Nama Barang *</label><input name="nama" class="form-control" value="{{ $v('nama') }}" required></div>
    <div class="col-md-4"><label class="form-label">Kategori *</label>
        <select name="kategori_id" class="form-select" required><option value="">Pilih...</option>
            @foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected($v('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach
        </select></div>
    <div class="col-md-4"><label class="form-label">Tanggal Pengadaan *</label><input type="date" name="tanggal_pengadaan" class="form-control" value="{{ $tgl }}" max="{{ now()->format('Y-m-d') }}" required></div>
    <div class="col-md-4"><label class="form-label">Nilai Awal (Rp) *</label><input type="number" min="0" name="nilai_awal" class="form-control" value="{{ $v('nilai_awal') }}" required></div>
    @if ($buat)
        <div class="col-md-6"><label class="form-label">Ruangan Awal *</label>
            <select name="ruangan_id" class="form-select" required><option value="">Pilih...</option>
                @foreach ($daftarRuangan as $r)<option value="{{ $r->id }}" @selected(old('ruangan_id') == $r->id)>{{ $r->nama }}</option>@endforeach
            </select></div>
        <div class="col-md-6"><label class="form-label">Kondisi Awal *</label>
            <select name="kondisi" class="form-select">
                @foreach (\App\Models\Barang::KONDISI as $kode => $label)<option value="{{ $kode }}" @selected(old('kondisi', 'baik') === $kode)>{{ $label }}</option>@endforeach
            </select></div>
    @endif
    <div class="col-12"><label class="form-label">Spesifikasi</label><textarea name="spesifikasi" rows="3" class="form-control" placeholder="Merk, tipe, ukuran, nomor seri, dll.">{{ $v('spesifikasi') }}</textarea></div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" rows="2" class="form-control">{{ $v('catatan') }}</textarea></div>
</div>
@if (!$buat)<div class="form-text mt-2">Lokasi diubah lewat tombol <b>Pindahkan</b> dan kondisi lewat <b>Ubah Kondisi</b> di halaman detail, supaya riwayatnya tercatat.</div>@endif
