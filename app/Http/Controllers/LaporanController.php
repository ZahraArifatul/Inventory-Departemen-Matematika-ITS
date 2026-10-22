<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Perbaikan;
use App\Services\Kelayakan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $tahunList = Perbaikan::pluck('tanggal_perbaikan')->map(fn ($d) => $d->year)->push(now()->year)->unique()->sortDesc()->values();

        return view('laporan.index', compact('tahunList'));
    }

    public function export(Request $request, string $jenis)
    {
        $nama = $jenis . '_' . now()->format('Ymd_His') . '.csv';

        return match ($jenis) {
            'barang' => $this->csv($nama, ['Kode', 'Nama', 'Kategori', 'Ruangan', 'Spesifikasi', 'Tanggal Pengadaan', 'Nilai Awal', 'Kondisi', 'Jumlah Perbaikan', 'Total Biaya Perbaikan'], function ($out) use ($request) {
                Barang::with(['kategori', 'ruangan'])->withCount('perbaikan')->withSum('perbaikan', 'biaya')
                    ->when($request->kondisi, fn ($q, $k) => $q->where('kondisi', $k))
                    ->orderBy('kode')->chunk(200, function ($baris) use ($out) {
                        foreach ($baris as $b) {
                            $this->tulis($out, [$b->kode, $b->nama, $b->kategori->nama, $b->ruangan->nama, $b->spesifikasi, $b->tanggal_pengadaan->format('Y-m-d'), $b->nilai_awal, $b->label_kondisi, $b->perbaikan_count, (int) $b->perbaikan_sum_biaya]);
                        }
                    });
            }),

            'perbaikan' => $this->csv($nama, ['Tanggal', 'Kode', 'Barang', 'Ruangan', 'Kerusakan', 'Rincian Perbaikan', 'Penanganan', 'Biaya', 'Diinput Oleh'], function ($out) use ($request) {
                Perbaikan::with(['barang.ruangan', 'petugas'])
                    ->when($request->tahun, fn ($q, $t) => $q->whereYear('tanggal_perbaikan', $t))
                    ->when($request->bulan, fn ($q, $b) => $q->whereMonth('tanggal_perbaikan', $b))
                    ->orderBy('tanggal_perbaikan')->orderBy('id')->chunk(200, function ($baris) use ($out) {
                        foreach ($baris as $p) {
                            $this->tulis($out, [$p->tanggal_perbaikan->format('Y-m-d'), $p->barang->kode, $p->barang->nama, $p->barang->ruangan->nama, $p->deskripsi_kerusakan, $p->rincian_perbaikan, $p->penanganan, $p->biaya, $p->petugas?->name]);
                        }
                    });
            }),

            'mutasi' => $this->csv($nama, ['Kode', 'Barang', 'Ruangan Asal', 'Ruangan Tujuan', 'Tanggal Masuk', 'Tanggal Keluar', 'Petugas', 'Keterangan'], function ($out) use ($request) {
                Mutasi::with(['barang', 'asal', 'tujuan', 'petugas'])
                    ->when($request->tahun, fn ($q, $t) => $q->whereYear('tanggal_masuk', $t))
                    ->when($request->bulan, fn ($q, $b) => $q->whereMonth('tanggal_masuk', $b))
                    ->orderBy('tanggal_masuk')->orderBy('id')->chunk(200, function ($baris) use ($out) {
                        foreach ($baris as $m) {
                            $this->tulis($out, [$m->barang->kode, $m->barang->nama, $m->asal?->nama ?? '-', $m->tujuan->nama, $m->tanggal_masuk->format('Y-m-d'), $m->tanggal_keluar?->format('Y-m-d') ?? 'Masih di sini', $m->petugas?->name, $m->keterangan]);
                        }
                    });
            }),

            'analisis' => $this->csv($nama, ['Kode', 'Nama', 'Kategori', 'Ruangan', 'Nilai Awal', 'Usia (tahun)', 'Umur Ekonomis (tahun)', 'Jumlah Perbaikan', 'Perbaikan 12 Bulan', 'Total Biaya Perbaikan', '% dari Nilai Awal', 'Skor', 'Rekomendasi', 'Alasan'], function ($out) {
                $semua = Kelayakan::semua()->sort(fn ($a, $b) => [$b['n']['skor'], $b['n']['total_biaya']] <=> [$a['n']['skor'], $a['n']['total_biaya']]);
                foreach ($semua as $r) {
                    $b = $r['barang'];
                    $n = $r['n'];
                    $this->tulis($out, [$b->kode, $b->nama, $b->kategori->nama, $b->ruangan->nama, $b->nilai_awal, number_format($n['usia'] ?? 0, 1, ',', ''), $n['umur'], $n['frekuensi'], $n['frek_tahun'], $n['total_biaya'], $n['rasio'] === null ? '' : round($n['rasio'] * 100) . '%', $n['skor'], $n['label'], implode('; ', $n['alasan'])]);
                }
            }),

            default => abort(404),
        };
    }

    /** CSV dengan pemisah titik koma + BOM, langsung terbuka rapi di Excel (pengaturan Indonesia). */
    private function csv(string $nama, array $header, \Closure $isi)
    {
        return response()->streamDownload(function () use ($header, $isi) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header, ';', '"', '\\');
            $isi($out);
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function tulis($out, array $baris): void
    {
        $baris = array_map(function ($v) {
            // cegah "CSV injection" jika teks diawali = + - @
            if (is_string($v) && $v !== '' && preg_match('/^[=+\-@\t\r]/', $v)) {
                return "'" . $v;
            }

            return $v;
        }, $baris);

        fputcsv($out, $baris, ';', '"', '\\');
    }
}
