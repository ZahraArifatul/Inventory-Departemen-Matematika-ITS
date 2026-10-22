<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('spesifikasi')->nullable();
            $table->foreignId('kategori_id')->constrained('kategori_barang');
            $table->foreignId('ruangan_id')->constrained('ruangan'); // lokasi saat ini
            $table->date('tanggal_pengadaan');
            $table->unsignedBigInteger('nilai_awal')->default(0);
            $table->string('kondisi', 20)->default('baik'); // baik | rusak | rusak_berat | dihapus
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
