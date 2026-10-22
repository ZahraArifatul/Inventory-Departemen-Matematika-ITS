<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::with(['category', 'lastMaintenance'])
            ->when($request->q, function ($q, $cari) {
                $q->where(fn ($w) => $w->where('nama', 'like', "%$cari%")
                    ->orWhere('kode', 'like', "%$cari%")
                    ->orWhere('lokasi', 'like', "%$cari%")
                    ->orWhere('merk', 'like', "%$cari%"));
            })
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('nama')->get();

        return view('assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        return view('assets.create', [
            'asset' => new Asset(['status' => 'aktif']),
            'categories' => Category::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $asset = Asset::create($this->validated($request));

        return redirect()->route('assets.show', $asset)->with('ok', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'maintenances', 'lastMaintenance']);

        return view('assets.show', [
            'asset' => $asset,
            'jenisList' => Maintenance::JENIS,
            'totalBiaya' => $asset->maintenances->sum('biaya'),
        ]);
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => Category::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $asset->update($this->validated($request, $asset));

        return redirect()->route('assets.show', $asset)->with('ok', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('ok', 'Aset berhasil dihapus.');
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('assets', 'kode')->ignore($asset?->id)],
            'nama' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'lokasi' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'interval_servis_bulan' => 'nullable|integer|min:1|max:120',
            'status' => 'required|in:aktif,nonaktif',
            'catatan' => 'nullable|string',
        ]);
    }
}
