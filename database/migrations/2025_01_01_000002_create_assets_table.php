<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('lokasi')->nullable();
            $table->string('merk')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->unsignedSmallInteger('interval_servis_bulan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
