<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'kode', 'nama', 'category_id', 'lokasi', 'merk', 'serial_number',
        'tanggal_beli', 'interval_servis_bulan', 'status', 'catatan',
    ];

    protected $casts = ['tanggal_beli' => 'date'];

    /** Berapa hari sebelum jatuh tempo dianggap "segera". */
    public const HARI_PERINGATAN = 30;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->orderByDesc('tanggal_servis');
    }

    public function lastMaintenance()
    {
        return $this->hasOne(Maintenance::class)->latestOfMany('tanggal_servis');
    }

    /** Tanggal servis terakhir (null jika belum pernah). */
    public function getTerakhirServisAttribute()
    {
        return $this->lastMaintenance?->tanggal_servis;
    }

    /** Jatuh tempo = servis terakhir (atau tanggal beli) + interval bulan. */
    public function getJatuhTempoAttribute()
    {
        if (!$this->interval_servis_bulan) {
            return null;
        }
        $dasar = $this->terakhir_servis ?? $this->tanggal_beli;

        return $dasar?->copy()->addMonths($this->interval_servis_bulan);
    }

    /** Selisih hari ke jatuh tempo (negatif = sudah lewat). */
    public function getSisaHariAttribute(): ?int
    {
        if (!$this->jatuh_tempo) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->jatuh_tempo->copy()->startOfDay(), false);
    }

    /** terlambat | segera | aman | tanpa_jadwal */
    public function getStatusServisAttribute(): string
    {
        $sisa = $this->sisa_hari;
        if ($sisa === null) {
            return 'tanpa_jadwal';
        }
        if ($sisa < 0) {
            return 'terlambat';
        }
        if ($sisa <= self::HARI_PERINGATAN) {
            return 'segera';
        }

        return 'aman';
    }

    public function getLabelStatusServisAttribute(): array
    {
        return match ($this->status_servis) {
            'terlambat' => ['Terlambat ' . abs($this->sisa_hari) . ' hari', 'danger'],
            'segera'    => ['Servis ' . ($this->sisa_hari == 0 ? 'hari ini' : $this->sisa_hari . ' hari lagi'), 'warning'],
            'aman'      => ['Aman', 'success'],
            default     => ['Tanpa jadwal', 'secondary'],
        };
    }
}
