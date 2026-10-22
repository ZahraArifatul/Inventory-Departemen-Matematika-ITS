<?php

namespace App\Services;

use App\Models\Barang;
use App\Support\Fmt;
use Illuminate\Support\Collection;

/**
 * Analisis kelayakan barang: masih layak diperbaiki, perlu dipantau,
 * atau disarankan dihapus dan dibeli baru.
 *
 * Dasar penilaian (skor). Angka ambang bisa disesuaikan di bawah ini.
 *  1. Total biaya perbaikan dibanding nilai awal  : >=30% (+1), >=50% (+2), >=75% (+3)
 *  2. Frekuensi perbaikan 12 bulan terakhir        : >=2x  (+1), >=3x  (+2), >=5x  (+3)
 *  3. Usia dibanding umur ekonomis kategori         : >=75% (+1), >=100% (+2)
 *  4. Kondisi saat ini Rusak Berat                  : +2
 * Skor >= 5 : disarankan diganti | 3-4 : perlu dipantau | < 3 : masih layak diperbaiki
 */
class Kelayakan
{
    public const SKOR_GANTI = 5;
    public const SKOR_PANTAU = 3;

    public const LABEL = [
        'layak' => ['Masih layak diperbaiki', 'success'],
        'pantau' => ['Perlu dipantau', 'warning'],
        'ganti' => ['Disarankan dihapus & dibeli baru', 'danger'],
        'dihapus' => ['Sudah dihapus', 'secondary'],
    ];

    public static function semua(): Collection
    {
        return Barang::denganAgregat()
            ->with(['kategori', 'ruangan'])
            ->where('kondisi', '!=', 'dihapus')
            ->get()
            ->map(fn (Barang $b) => ['barang' => $b, 'n' => self::nilai($b)]);
    }

    public static function nilai(Barang $b): array
    {
        $total = (int) ($b->perbaikan_sum_biaya ?? 0);
        $frek = (int) ($b->perbaikan_count ?? 0);
        $frekTahun = (int) ($b->perbaikan_setahun_count ?? 0);
        $rasio = $b->nilai_awal > 0 ? $total / $b->nilai_awal : null;
        $usia = $b->usia_tahun;
        $umur = $b->kategori?->umur_ekonomis_tahun;
        $rasioUsia = ($umur && $usia !== null) ? $usia / $umur : null;

        $dasar = [
            'total_biaya' => $total,
            'rasio' => $rasio,
            'frekuensi' => $frek,
            'frek_tahun' => $frekTahun,
            'usia' => $usia,
            'umur' => $umur,
            'rasio_usia' => $rasioUsia,
        ];

        if ($b->kondisi === 'dihapus') {
            return $dasar + ['skor' => 0, 'rekomendasi' => 'dihapus', 'label' => self::LABEL['dihapus'][0], 'warna' => 'secondary', 'alasan' => []];
        }

        $skor = 0;
        $alasan = [];

        if ($rasio !== null) {
            $tambah = $rasio >= 0.75 ? 3 : ($rasio >= 0.5 ? 2 : ($rasio >= 0.3 ? 1 : 0));
            if ($tambah) {
                $skor += $tambah;
                $alasan[] = 'Total biaya perbaikan ' . Fmt::rp($total) . ' = ' . Fmt::persen($rasio) . ' dari nilai awal';
            }
        }

        $tambah = $frekTahun >= 5 ? 3 : ($frekTahun >= 3 ? 2 : ($frekTahun >= 2 ? 1 : 0));
        if ($tambah) {
            $skor += $tambah;
            $alasan[] = 'Diperbaiki ' . $frekTahun . 'x dalam 12 bulan terakhir';
        }

        if ($rasioUsia !== null) {
            $tambah = $rasioUsia >= 1 ? 2 : ($rasioUsia >= 0.75 ? 1 : 0);
            if ($tambah) {
                $skor += $tambah;
                $alasan[] = 'Usia ' . number_format($usia, 1, ',', '.') . ' tahun, umur ekonomis kategori ' . $umur . ' tahun';
            }
        }

        if ($b->kondisi === 'rusak_berat') {
            $skor += 2;
            $alasan[] = 'Kondisi saat ini Rusak Berat';
        }

        $rekomendasi = $skor >= self::SKOR_GANTI ? 'ganti' : ($skor >= self::SKOR_PANTAU ? 'pantau' : 'layak');

        return $dasar + [
            'skor' => $skor,
            'rekomendasi' => $rekomendasi,
            'label' => self::LABEL[$rekomendasi][0],
            'warna' => self::LABEL[$rekomendasi][1],
            'alasan' => $alasan,
        ];
    }
}
