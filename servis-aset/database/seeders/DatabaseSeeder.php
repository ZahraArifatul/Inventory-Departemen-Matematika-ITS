<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'AC' => 3, 'Printer' => 6, 'Komputer / Laptop' => 12, 'Genset' => 6,
            'Dispenser' => 6, 'Kendaraan' => 6, 'CCTV' => 12, 'Lainnya' => null,
        ];
        $cat = [];
        foreach ($kategori as $nama => $interval) {
            $cat[$nama] = Category::firstOrCreate(['nama' => $nama], ['interval_default_bulan' => $interval]);
        }

        // Data contoh (boleh dihapus)
        $contoh = [
            ['AC-001', 'AC Ruang Meeting', 'AC', 'Ruang Meeting', 'Daikin', 3, now()->subMonths(4)],
            ['AC-002', 'AC Ruang Direktur', 'AC', 'Lantai 2', 'Panasonic', 3, now()->subMonths(2)],
            ['PRN-001', 'Printer Kasir', 'Printer', 'Kasir', 'Epson L3210', 6, now()->subMonths(5)->subDays(20)],
            ['PRN-002', 'Printer Admin', 'Printer', 'Ruang Admin', 'Canon G2010', 6, now()->subMonth()],
            ['GEN-001', 'Genset Cadangan', 'Genset', 'Belakang Gedung', 'Kubota', 6, null],
        ];
        foreach ($contoh as [$kode, $nama, $kat, $lokasi, $merk, $interval, $terakhir]) {
            $a = Asset::firstOrCreate(['kode' => $kode], [
                'nama' => $nama, 'category_id' => $cat[$kat]->id, 'lokasi' => $lokasi,
                'merk' => $merk, 'interval_servis_bulan' => $interval,
                'tanggal_beli' => now()->subYears(2), 'status' => 'aktif',
            ]);
            if ($terakhir && !$a->maintenances()->exists()) {
                $a->maintenances()->create([
                    'tanggal_servis' => $terakhir, 'jenis' => 'Servis rutin',
                    'teknisi' => 'Teknisi Contoh', 'biaya' => 150000,
                ]);
            }
        }
    }
}
