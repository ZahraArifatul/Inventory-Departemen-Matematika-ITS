<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perbaikan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang');
            $table->date('tanggal_perbaikan');
            $table->text('deskripsi_kerusakan');
            $table->text('rincian_perbaikan')->nullable();
            $table->string('penanganan'); // nama teknisi atau vendor/pihak ketiga
            $table->foreignId('petugas_id')->nullable()->constrained('users'); // yang menginput
            $table->unsignedBigInteger('biaya')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbaikan_barang');
    }
};
