<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = 'mutasi_barang';

    protected $fillable = [
        'barang_id', 'ruangan_asal_id', 'ruangan_tujuan_id',
        'tanggal_masuk', 'tanggal_keluar', 'petugas_id', 'keterangan',
    ];

    protected $casts = ['tanggal_masuk' => 'date', 'tanggal_keluar' => 'date'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function asal()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_asal_id');
    }

    public function tujuan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
