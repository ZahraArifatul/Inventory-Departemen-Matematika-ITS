<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::query()
            ->when($request->tahun, fn ($q, $t) => $q->whereYear('tanggal_servis', $t))
            ->when($request->bulan, fn ($q, $b) => $q->whereMonth('tanggal_servis', $b))
            ->when($request->jenis, fn ($q, $j) => $q->where('jenis', $j))
            ->when($request->category_id, fn ($q, $id) => $q->whereHas('asset', fn ($a) => $a->where('category_id', $id)))
            ->when($request->q, function ($q, $cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('kerusakan', 'like', "%$cari%")
                        ->orWhere('keterangan', 'like', "%$cari%")
                        ->orWhere('teknisi', 'like', "%$cari%")
                        ->orWhereHas('asset', fn ($a) => $a->where('nama', 'like', "%$cari%")->orWhere('kode', 'like', "%$cari%"));
                });
            });

        $jumlah = (clone $query)->count();
        $totalBiaya = (clone $query)->sum('biaya');

        // Aset yang paling sering muncul pada periode/filter ini (data mentah, bukan kesimpulan)
        $teratas = (clone $query)
            ->select('asset_id', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(biaya) as total_biaya'))
            ->groupBy('asset_id')
            ->orderByDesc('jumlah')
            ->orderByDesc('total_biaya')
            ->limit(5)
            ->with('asset')
            ->get();

        $maintenances = $query->with('asset.category')
            ->latest('tanggal_servis')->latest('id')
            ->paginate(20)->withQueryString();

        $tahunList = Maintenance::pluck('tanggal_servis')
            ->map(fn ($d) => $d->year)->push(now()->year)->unique()->sortDesc()->values();

        return view('maintenances.index', [
            'maintenances' => $maintenances,
            'jumlah' => $jumlah,
            'totalBiaya' => $totalBiaya,
            'teratas' => $teratas,
            'tahunList' => $tahunList,
            'categories' => Category::orderBy('nama')->get(),
            'jenisList' => Maintenance::JENIS,
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $asset->maintenances()->create($this->validated($request));

        return redirect()->route('assets.show', $asset)->with('ok', 'Riwayat servis dicatat.');
    }

    public function edit(Maintenance $maintenance)
    {
        $maintenance->load('asset');

        return view('maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $maintenance->update($this->validated($request));

        return redirect()->route('assets.show', $maintenance->asset_id)->with('ok', 'Riwayat servis diperbarui.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $asset = $maintenance->asset;
        $maintenance->delete();

        return redirect()->route('assets.show', $asset)->with('ok', 'Riwayat servis dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'tanggal_servis' => 'required|date|before_or_equal:today',
            'jenis' => 'required|string|max:100',
            'teknisi' => 'nullable|string|max:255',
            'kerusakan' => 'nullable|string',
            'biaya' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);
        $data['biaya'] = $data['biaya'] ?? 0;

        return $data;
    }
}
