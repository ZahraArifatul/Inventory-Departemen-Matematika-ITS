<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Perbaikan;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\InventarisService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Akun awal (GANTI password setelah login pertama) ----
        $akun = [
            ['Admin Departemen', 'admin@its.ac.id', 'admin'],
            ['Teknisi Contoh', 'teknisi@its.ac.id', 'teknisi'],
            ['Manajemen Contoh', 'manajemen@its.ac.id', 'manajemen'],
        ];
        foreach ($akun as [$nama, $email, $role]) {
            User::firstOrCreate(['email' => $email], [
                'name' => $nama, 'password' => Hash::make('password'), 'role' => $role, 'aktif' => true,
            ]);
        }
        $teknisi = User::where('email', 'teknisi@its.ac.id')->first();

        // ---- Master data ----
        $umur = [
            'Komputer / Laptop' => 5, 'Printer' => 5, 'AC' => 8, 'Proyektor' => 5,
            'Perangkat Jaringan' => 5, 'Furnitur' => 10, 'Peralatan Lab' => 8, 'Lainnya' => null,
        ];
        $kat = [];
        foreach ($umur as $nama => $th) {
            $kat[$nama] = Kategori::firstOrCreate(['nama' => $nama], ['umur_ekonomis_tahun' => $th]);
        }

        $ruang = [];
        foreach (['Ruang Dosen', 'Laboratorium A', 'Laboratorium B', 'Ruang Administrasi', 'Ruang Rapat', 'Gudang'] as $nama) {
            $ruang[$nama] = Ruangan::firstOrCreate(['nama' => $nama]);
        }

        // ---- Data contoh (boleh dihapus) ----
        if (Barang::exists()) {
            return;
        }
        $layanan = app(InventarisService::class);

        $contoh = [
            ['INV-001', 'Laptop Dell Latitude 5420', 'Komputer / Laptop', 'Ruang Dosen', 4, 14500000, 'baik', 'Core i5, RAM 16 GB, SSD 512 GB', [[8, 'Baterai drop', 'Ganti baterai', 850000]]],
            ['INV-002', 'Printer Epson L3110', 'Printer', 'Ruang Administrasi', 5.5, 2500000, 'rusak_berat', 'Printer inkjet tank', [[14, 'Kertas sering macet', 'Ganti roller', 350000], [9, 'Hasil cetak bergaris', 'Head cleaning dan ganti tinta', 300000], [5, 'Tidak bisa menarik kertas', 'Ganti roller ulang', 380000], [3, 'Lampu indikator berkedip', 'Reset waste ink pad', 400000], [1, 'Mati total', 'Cek power supply', 150000]]],
            ['INV-003', 'AC Daikin 1 PK', 'AC', 'Laboratorium A', 3, 5200000, 'baik', 'Split 1 PK, inverter', [[6, 'Kurang dingin', 'Isi freon', 450000]]],
            ['INV-004', 'AC Panasonic 2 PK', 'AC', 'Ruang Rapat', 7, 8500000, 'rusak', 'Split 2 PK', [[18, 'Bocor air', 'Bersihkan saluran dan ganti pipa', 600000], [10, 'Tidak dingin', 'Isi freon dan cek kebocoran', 900000], [4, 'Kompresor berisik', 'Ganti kapasitor', 1200000]]],
            ['INV-005', 'Proyektor Epson EB-X06', 'Proyektor', 'Ruang Rapat', 2, 6800000, 'baik', 'XGA 3600 lumen', []],
            ['INV-006', 'PC Desktop Lab', 'Komputer / Laptop', 'Laboratorium B', 6, 9000000, 'baik', 'Core i5 gen 8, RAM 8 GB', [[11, 'Hardisk bad sector', 'Ganti SSD', 650000]]],
            ['INV-007', 'Switch Cisco 24 Port', 'Perangkat Jaringan', 'Gudang', 3, 7200000, 'baik', 'Managed switch', []],
        ];

        foreach ($contoh as [$kode, $nama, $k, $r, $usiaTh, $nilai, $kondisi, $spek, $perbaikan]) {
            $tglBeli = now()->subDays((int) round($usiaTh * 365))->startOfDay();
            $barang = $layanan->tambahBarang([
                'kode' => $kode, 'nama' => $nama, 'spesifikasi' => $spek,
                'kategori_id' => $kat[$k]->id, 'ruangan_id' => $ruang[$r]->id,
                'tanggal_pengadaan' => $tglBeli, 'nilai_awal' => $nilai, 'kondisi' => $kondisi,
            ], $teknisi->id);

            foreach ($perbaikan as [$bulanLalu, $rusak, $tindakan, $biaya]) {
                Perbaikan::create([
                    'barang_id' => $barang->id,
                    'tanggal_perbaikan' => now()->subMonths($bulanLalu)->startOfDay()->max($tglBeli),
                    'deskripsi_kerusakan' => $rusak, 'rincian_perbaikan' => $tindakan,
                    'penanganan' => 'Teknisi Contoh', 'petugas_id' => $teknisi->id, 'biaya' => $biaya,
                ]);
            }
        }

        // contoh perpindahan ruangan
        $pc = Barang::where('kode', 'INV-006')->first();
        $layanan->pindahkan($pc, $ruang['Laboratorium A']->id, now()->subMonths(3)->toDateString(), 'Penataan ulang lab', $teknisi->id);
        $sw = Barang::where('kode', 'INV-007')->first();
        $layanan->pindahkan($sw, $ruang['Ruang Administrasi']->id, now()->subMonths(1)->toDateString(), 'Dipasang di ruang admin', $teknisi->id);
    }
}
