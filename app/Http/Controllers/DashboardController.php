<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Perbaikan;
use App\Models\Ruangan;
use App\Services\Kelayakan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $perKondisi = Barang::select('kondisi', DB::raw('COUNT(*) as jml'))->groupBy('kondisi')->pluck('jml', 'kondisi');

        $perRuangan = Ruangan::withCount(['barang as jumlah_barang' => fn ($q) => $q->where('kondisi', '!=', 'dihapus')])
            ->orderBy('nama')->get();

        $perhatian = Barang::with(['ruangan', 'kategori'])
            ->whereIn('kondisi', ['rusak', 'rusak_berat'])
            ->orderByDesc('updated_at')->take(8)->get();

        $perbaikanTerbaru = Perbaikan::with('barang')->orderByDesc('tanggal_perbaikan')->orderByDesc('id')->take(6)->get();
        $mutasiTerbaru = Mutasi::with(['barang', 'asal', 'tujuan'])->orderByDesc('id')->take(6)->get();

        $biayaTahunIni = Perbaikan::whereYear('tanggal_perbaikan', now()->year)->sum('biaya');
        $disarankanGanti = Kelayakan::semua()->filter(fn ($r) => $r['n']['rekomendasi'] === 'ganti')->count();

        return view('dashboard', compact('perKondisi', 'perRuangan', 'perhatian', 'perbaikanTerbaru', 'mutasiTerbaru', 'biayaTahunIni', 'disarankanGanti'));
    }
}
