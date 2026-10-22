<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Mutasi;
use Illuminate\Support\Facades\DB;

class InventarisService
{
    /** Simpan barang baru + catat penempatan awal di riwayat. */
    public function tambahBarang(array $data, int $petugasId): Barang
    {
        return DB::transaction(function () use ($data, $petugasId) {
            $barang = Barang::create($data);

            Mutasi::create([
                'barang_id' => $barang->id,
                'ruangan_asal_id' => null,
                'ruangan_tujuan_id' => $barang->ruangan_id,
                'tanggal_masuk' => $barang->tanggal_pengadaan,
                'tanggal_keluar' => null,
                'petugas_id' => $petugasId,
                'keterangan' => 'Penempatan awal',
            ]);

            return $barang;
        });
    }

    /**
     * Pindahkan barang: tutup riwayat ruangan lama (tanggal keluar),
     * buat riwayat ruangan baru, dan perbarui lokasi barang.
     */
    public function pindahkan(Barang $barang, int $ruanganTujuanId, string $tanggal, ?string $keterangan, int $petugasId): void
    {
        DB::transaction(function () use ($barang, $ruanganTujuanId, $tanggal, $keterangan, $petugasId) {
            $aktif = Mutasi::where('barang_id', $barang->id)
                ->whereNull('tanggal_keluar')
                ->orderByDesc('id')
                ->first();

            if ($aktif) {
                $aktif->update(['tanggal_keluar' => $tanggal]);
            }

            Mutasi::create([
                'barang_id' => $barang->id,
                'ruangan_asal_id' => $barang->ruangan_id,
                'ruangan_tujuan_id' => $ruanganTujuanId,
                'tanggal_masuk' => $tanggal,
                'tanggal_keluar' => null,
                'petugas_id' => $petugasId,
                'keterangan' => $keterangan,
            ]);

            $barang->update(['ruangan_id' => $ruanganTujuanId]);
        });
    }
}
