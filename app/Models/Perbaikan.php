<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    protected $table = 'perbaikan_barang';

    protected $fillable = [
        'barang_id', 'tanggal_perbaikan', 'deskripsi_kerusakan', 'rincian_perbaikan',
        'penanganan', 'petugas_id', 'biaya',
    ];

    protected $casts = ['tanggal_perbaikan' => 'date', 'biaya' => 'integer'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
