<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori_barang';

    protected $fillable = ['nama', 'umur_ekonomis_tahun'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
