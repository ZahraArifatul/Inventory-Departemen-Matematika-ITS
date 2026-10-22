<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = ['asset_id', 'tanggal_servis', 'jenis', 'teknisi', 'kerusakan', 'biaya', 'keterangan'];

    protected $casts = ['tanggal_servis' => 'date'];

    public const JENIS = ['Servis rutin', 'Perbaikan', 'Ganti sparepart', 'Pembersihan', 'Kalibrasi', 'Lainnya'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
