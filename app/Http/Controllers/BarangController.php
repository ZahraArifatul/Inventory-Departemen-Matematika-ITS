<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Services\InventarisService;
use App\Services\Kelayakan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barang = Barang::with(['kategori', 'ruangan'])
            ->withCount('perbaikan')->withSum('perbaikan', 'biaya')
            ->when($request->q, function ($q, $cari) {
                $q->where(fn ($w) => $w->where('nama', 'like', "%$cari%")
                    ->orWhere('kode', 'like', "%$cari%")
                    ->orWhere('spesifikasi', 'like', "%$cari%"));
            })
            ->when($request->kategori_id, fn ($q, $id) => $q->where('kategori_id', $id))
            ->when($request->ruangan_id, fn ($q, $id) => $q->where('ruangan_id', $id))
            ->when($request->kondisi, fn ($q, $k) => $q->where('kondisi', $k))
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('barang.index', [
            'barang' => $barang,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('barang.create', [
            'barang' => new Barang(['kondisi' => 'baik', 'tanggal_pengadaan' => now()]),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, InventarisService $layanan)
    {
        $barang = $layanan->tambahBarang($this->validasi($request), $request->user()->id);

        return redirect()->route('barang.show', $barang)->with('ok', 'Barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'kategori', 'ruangan',
            'mutasi' => fn ($q) => $q->with(['asal', 'tujuan', 'petugas'])->orderByDesc('tanggal_masuk')->orderByDesc('id'),
            'perbaikan' => fn ($q) => $q->with('petugas')->orderByDesc('tanggal_perbaikan')->orderByDesc('id'),
        ])->muatAgregat();

        return view('barang.show', [
            'barang' => $barang,
            'nilai' => Kelayakan::nilai($barang),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', [
            'barang' => $barang,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarRuangan' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $barang->update($this->validasi($request, $barang));

        return redirect()->route('barang.show', $barang)->with('ok', 'Data barang diperbarui.');
    }

    public function ubahKondisi(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'kondisi' => ['required', Rule::in(array_keys(Barang::KONDISI))],
        ]);
        $barang->update($data);

        return back()->with('ok', 'Kondisi barang diubah menjadi ' . $barang->label_kondisi . '.');
    }

    private function validasi(Request $request, ?Barang $barang = null): array
    {
        $aturan = [
            'kode' => ['required', 'string', 'max:50', Rule::unique('barang', 'kode')->ignore($barang?->id)],
            'nama' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
            'kategori_id' => 'required|exists:kategori_barang,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'nilai_awal' => 'required|integer|min:0',
            'catatan' => 'nullable|string',
        ];

        if (!$barang) { // saat tambah baru: lokasi awal & kondisi awal
            $aturan['ruangan_id'] = 'required|exists:ruangan,id';
            $aturan['kondisi'] = ['required', Rule::in(array_keys(Barang::KONDISI))];
        }

        return $request->validate($aturan);
    }
}
