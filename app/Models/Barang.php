<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode', 'nama', 'spesifikasi', 'kategori_id', 'ruangan_id',
        'tanggal_pengadaan', 'nilai_awal', 'kondisi', 'catatan',
    ];

    protected $casts = ['tanggal_pengadaan' => 'date', 'nilai_awal' => 'integer'];

    public const KONDISI = [
        'baik' => 'Baik',
        'rusak' => 'Rusak',
        'rusak_berat' => 'Rusak Berat',
        'dihapus' => 'Dihapus',
    ];

    public const WARNA = [
        'baik' => 'success',
        'rusak' => 'warning',
        'rusak_berat' => 'danger',
        'dihapus' => 'secondary',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function mutasi()
    {
        return $this->hasMany(Mutasi::class, 'barang_id');
    }

    public function perbaikan()
    {
        return $this->hasMany(Perbaikan::class, 'barang_id');
    }

    /** Menambahkan jumlah perbaikan, jumlah 12 bulan terakhir, dan total biaya. */
    public function scopeDenganAgregat(Builder $query): Builder
    {
        return $query
            ->withCount([
                'perbaikan',
                'perbaikan as perbaikan_setahun_count' => fn ($q) => $q->where('tanggal_perbaikan', '>=', now()->subYear()->toDateString()),
            ])
            ->withSum('perbaikan', 'biaya');
    }

    public function muatAgregat(): static
    {
        $this->loadCount([
            'perbaikan',
            'perbaikan as perbaikan_setahun_count' => fn ($q) => $q->where('tanggal_perbaikan', '>=', now()->subYear()->toDateString()),
        ]);
        $this->loadSum('perbaikan', 'biaya');

        return $this;
    }

    public function getUsiaTahunAttribute(): ?float
    {
        if (!$this->tanggal_pengadaan) {
            return null;
        }

        return abs($this->tanggal_pengadaan->diffInDays(now())) / 365.25;
    }

    public function getLabelKondisiAttribute(): string
    {
        return self::KONDISI[$this->kondisi] ?? $this->kondisi;
    }

    public function getWarnaKondisiAttribute(): string
    {
        return self::WARNA[$this->kondisi] ?? 'secondary';
    }
}
