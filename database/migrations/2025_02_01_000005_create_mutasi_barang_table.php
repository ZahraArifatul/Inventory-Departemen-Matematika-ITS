<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Audit trail: baris riwayat tidak pernah dihapus lewat aplikasi.
        Schema::create('mutasi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang');
            $table->foreignId('ruangan_asal_id')->nullable()->constrained('ruangan');
            $table->foreignId('ruangan_tujuan_id')->constrained('ruangan');
            $table->date('tanggal_masuk');            // mulai ditempatkan di ruangan tujuan
            $table->date('tanggal_keluar')->nullable(); // null = masih di ruangan itu
            $table->foreignId('petugas_id')->nullable()->constrained('users');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang');
    }
};
