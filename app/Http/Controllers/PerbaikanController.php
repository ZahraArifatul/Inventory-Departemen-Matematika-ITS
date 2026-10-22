<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Perbaikan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerbaikanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perbaikan::query()
            ->when($request->tahun, fn ($q, $t) => $q->whereYear('tanggal_perbaikan', $t))
            ->when($request->bulan, fn ($q, $b) => $q->whereMonth('tanggal_perbaikan', $b))
            ->when($request->kategori_id, fn ($q, $id) => $q->whereHas('barang', fn ($b) => $b->where('kategori_id', $id)))
            ->when($request->ruangan_id, fn ($q, $id) => $q->whereHas('barang', fn ($b) => $b->where('ruangan_id', $id)))
            ->when($request->q, function ($q, $cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('deskripsi_kerusakan', 'like', "%$cari%")
                        ->orWhere('rincian_perbaikan', 'like', "%$cari%")
                        ->orWhere('penanganan', 'like', "%$cari%")
                        ->orWhereHas('barang', fn ($b) => $b->where('nama', 'like', "%$cari%")->orWhere('kode', 'like', "%$cari%"));
                });
            });

        $jumlah = (clone $query)->count();
        $totalBiaya = (clone $query)->sum('biaya');

        $teratas = (clone $query)
            ->select('barang_id', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(biaya) as total_biaya'))
            ->groupBy('barang_id')
            ->orderByDesc('jumlah')->orderByDesc('total_biaya')
            ->limit(5)->with('barang')->get();

        $perbaikan = $query->with(['barang.kategori', 'barang.ruangan', 'petugas'])
            ->orderByDesc('tanggal_perbaikan')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        $tahunList = Perbaikan::pluck('tanggal_perbaikan')->map(fn ($d) => $d->year)->push(now()->year)->unique()->sortDesc()->values();

        return view('perbaikan.index', [
            'perbaikan' => $perbaikan,
            'jumlah' => $jumlah,
            'totalBiaya' => $totalBiaya,
            'teratas' => $teratas,
            'tahunList' => $tahunList,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, Barang $barang)
    {
        if ($barang->kondisi === 'dihapus') {
            return back()->withErrors(['deskripsi_kerusakan' => 'Barang yang sudah dihapus tidak bisa dicatat perbaikannya.']);
        }

        $data = $this->validasi($request, true);
        $kondisiBaru = $data['kondisi_setelah'] ?? null;
        unset($data['kondisi_setelah']);

        $data['barang_id'] = $barang->id;
        $data['petugas_id'] = $request->user()->id;
        Perbaikan::create($data);

        if ($kondisiBaru) {
            $barang->update(['kondisi' => $kondisiBaru]);
        }

        return redirect()->route('barang.show', $barang)->with('ok', 'Catatan perbaikan disimpan.');
    }

    public function edit(Perbaikan $perbaikan)
    {
        $perbaikan->load('barang');

        return view('perbaikan.edit', compact('perbaikan'));
    }

    public function update(Request $request, Perbaikan $perbaikan)
    {
        $perbaikan->update($this->validasi($request, false));

        return redirect()->route('barang.show', $perbaikan->barang_id)->with('ok', 'Catatan perbaikan diperbarui.');
    }

    private function validasi(Request $request, bool $denganKondisi): array
    {
        $aturan = [
            'tanggal_perbaikan' => 'required|date|before_or_equal:today',
            'deskripsi_kerusakan' => 'required|string',
            'rincian_perbaikan' => 'nullable|string',
            'penanganan' => 'required|string|max:255',
            'biaya' => 'nullable|integer|min:0',
        ];
        if ($denganKondisi) {
            $aturan['kondisi_setelah'] = ['nullable', Rule::in(array_keys(Barang::KONDISI))];
        }

        $data = $request->validate($aturan);
        $data['biaya'] = $data['biaya'] ?? 0;

        return $data;
    }
}
