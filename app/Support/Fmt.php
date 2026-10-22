<?php

namespace App\Support;

class Fmt
{
    public static function rp($n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    public static function persen($r): string
    {
        return $r === null ? '-' : number_format($r * 100, 0, ',', '.') . '%';
    }

    public static function tgl($d, string $format = 'd M Y'): string
    {
        return $d ? $d->translatedFormat($format) : '-';
    }
}
