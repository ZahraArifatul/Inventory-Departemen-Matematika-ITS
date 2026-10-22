<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nama', 'interval_default_bulan'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
