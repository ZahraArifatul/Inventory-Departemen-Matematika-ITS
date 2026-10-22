@php $v = fn ($f) => old($f, $asset->{$f} ?? ''); @endphp
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">Kode Aset *</label><input name="kode" class="form-control" value="{{ $v('kode') }}" required placeholder="AC-001"></div>
    <div class="col-md-8"><label class="form-label">Nama Aset *</label><input name="nama" class="form-control" value="{{ $v('nama') }}" required placeholder="AC Ruang Meeting"></div>
    <div class="col-md-4">
        <label class="form-label">Kategori *</label>
        <select name="category_id" id="kategori" class="form-select" required>
            <option value="">Pilih...</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" data-interval="{{ $c->interval_default_bulan }}" @selected($v('category_id') == $c->id)>{{ $c->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">Lokasi</label><input name="lokasi" class="form-control" value="{{ $v('lokasi') }}"></div>
    <div class="col-md-4"><label class="form-label">Merk / Model</label><input name="merk" class="form-control" value="{{ $v('merk') }}"></div>
    <div class="col-md-4"><label class="form-label">Serial Number</label><input name="serial_number" class="form-control" value="{{ $v('serial_number') }}"></div>
    <div class="col-md-4"><label class="form-label">Tanggal Beli</label><input type="date" name="tanggal_beli" class="form-control" value="{{ old('tanggal_beli', $asset->tanggal_beli?->format('Y-m-d')) }}"></div>
    <div class="col-md-4">
        <label class="form-label">Interval Servis (bulan)</label>
        <input type="number" min="1" name="interval_servis_bulan" id="interval" class="form-control" value="{{ $v('interval_servis_bulan') }}" placeholder="mis. 3">
        <div class="form-text">Kosongkan jika tidak ada jadwal rutin.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="aktif" @selected($v('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected($v('status') === 'nonaktif')>Nonaktif</option>
        </select>
    </div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" rows="3" class="form-control">{{ $v('catatan') }}</textarea></div>
</div>
<script>
    // Isi interval otomatis dari kategori jika masih kosong
    document.getElementById('kategori').addEventListener('change', function () {
        const iv = document.getElementById('interval');
        const d = this.selectedOptions[0]?.dataset.interval;
        if (!iv.value && d) iv.value = d;
    });
</script>
