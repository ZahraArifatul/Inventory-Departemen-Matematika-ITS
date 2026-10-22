<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Ruangan;
use App\Services\Kelayakan;
use Illuminate\Http\Request;

class AnalisisController extends Controller
{
    public function index(Request $request)
    {
        $semua = Kelayakan::semua();

        $ringkasan = [
            'ganti' => $semua->filter(fn ($r) => $r['n']['rekomendasi'] === 'ganti')->count(),
            'pantau' => $semua->filter(fn ($r) => $r['n']['rekomendasi'] === 'pantau')->count(),
            'layak' => $semua->filter(fn ($r) => $r['n']['rekomendasi'] === 'layak')->count(),
        ];

        $hasil = $semua
            ->when($request->kategori_id, fn ($c, $id) => $c->filter(fn ($r) => $r['barang']->kategori_id == $id))
            ->when($request->ruangan_id, fn ($c, $id) => $c->filter(fn ($r) => $r['barang']->ruangan_id == $id))
            ->when($request->rekomendasi, fn ($c, $rk) => $c->filter(fn ($r) => $r['n']['rekomendasi'] === $rk))
            ->sort(fn ($a, $b) => [$b['n']['skor'], $b['n']['total_biaya']] <=> [$a['n']['skor'], $a['n']['total_biaya']])
            ->values();

        return view('analisis.index', [
            'hasil' => $hasil,
            'ringkasan' => $ringkasan,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }
}
