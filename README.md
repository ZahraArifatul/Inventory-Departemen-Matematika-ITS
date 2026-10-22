# Sistem Inventaris Barang - Departemen Matematika ITS (Laravel)

Fitur sesuai dokumen kebutuhan:
- Data barang (kode, nama, spesifikasi, kategori, tanggal pengadaan, nilai awal) + kondisi: Baik, Rusak, Rusak Berat, Dihapus
- Lokasi barang + riwayat perpindahan otomatis (ruangan asal/tujuan, tanggal masuk/keluar, petugas)
- Riwayat perbaikan (tanggal, kerusakan, rincian, teknisi/vendor, biaya) + akumulasi biaya
- Dashboard monitoring dan analisis kelayakan (biaya perbaikan vs nilai awal, frekuensi, usia)
- Login 3 peran: Admin, Teknisi, Manajemen (hak akses sesuai matriks)
- Ekspor Excel (CSV) dan cetak/PDF
- Audit trail: riwayat perpindahan dan perbaikan tidak bisa dihapus dari aplikasi

## Pasang ke proyek Laravel yang sudah ada
1. (Jika sebelumnya memasang versi "servis aset") jalankan `hapus-file-lama.bat C:\laragon\www\NAMA-PROYEK`
2. Salin folder `app`, `database`, `routes`, `resources` ke proyek (timpa).
3. Pastikan `.env` sudah mengarah ke database MySQL Anda.
4. Di folder proyek: `php artisan migrate:fresh --seed`  (menghapus SEMUA tabel lalu membuat ulang)
5. `php artisan serve` lalu buka http://localhost:8000

## Akun awal (password: `password`) - segera ganti
- admin@its.ac.id (Admin)
- teknisi@its.ac.id (Teknisi)
- manajemen@its.ac.id (Manajemen)

## Menyesuaikan analisis kelayakan
Ambang skor ada di `app/Services/Kelayakan.php`. Umur ekonomis per kategori diatur di menu Master Data > Kategori Barang.
