<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';

    protected $fillable = ['nama', 'keterangan'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'ruangan_id');
    }
}
