<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Ruangan;
use App\Services\InventarisService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $mutasi = Mutasi::with(['barang', 'asal', 'tujuan', 'petugas'])
            ->when($request->tahun, fn ($q, $t) => $q->whereYear('tanggal_masuk', $t))
            ->when($request->bulan, fn ($q, $b) => $q->whereMonth('tanggal_masuk', $b))
            ->when($request->ruangan_id, fn ($q, $id) => $q->where(fn ($w) => $w->where('ruangan_asal_id', $id)->orWhere('ruangan_tujuan_id', $id)))
            ->when($request->q, fn ($q, $c) => $q->whereHas('barang', fn ($b) => $b->where('nama', 'like', "%$c%")->orWhere('kode', 'like', "%$c%")))
            ->orderByDesc('tanggal_masuk')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        $tahunList = Mutasi::pluck('tanggal_masuk')->map(fn ($d) => $d->year)->push(now()->year)->unique()->sortDesc()->values();

        return view('mutasi.index', [
            'mutasi' => $mutasi,
            'tahunList' => $tahunList,
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, Barang $barang, InventarisService $layanan)
    {
        if ($barang->kondisi === 'dihapus') {
            return back()->withErrors(['ruangan_tujuan_id' => 'Barang yang sudah dihapus tidak bisa dipindahkan.']);
        }

        $aktif = Mutasi::where('barang_id', $barang->id)->whereNull('tanggal_keluar')->orderByDesc('id')->first();
        $min = $aktif?->tanggal_masuk?->format('Y-m-d');

        $aturanTanggal = ['required', 'date', 'before_or_equal:today'];
        if ($min) {
            $aturanTanggal[] = 'after_or_equal:' . $min;
        }

        $data = $request->validate([
            'ruangan_tujuan_id' => ['required', 'exists:ruangan,id', Rule::notIn([$barang->ruangan_id])],
            'tanggal_pindah' => $aturanTanggal,
            'keterangan' => 'nullable|string|max:255',
        ], [
            'ruangan_tujuan_id.not_in' => 'Ruangan tujuan sama dengan ruangan barang saat ini.',
            'tanggal_pindah.after_or_equal' => 'Tanggal pindah tidak boleh sebelum tanggal masuk di ruangan sekarang.',
        ]);

        $layanan->pindahkan($barang, (int) $data['ruangan_tujuan_id'], $data['tanggal_pindah'], $data['keterangan'] ?? null, $request->user()->id);

        return redirect()->route('barang.show', $barang)->with('ok', 'Barang dipindahkan dan riwayat tercatat.');
    }
}
