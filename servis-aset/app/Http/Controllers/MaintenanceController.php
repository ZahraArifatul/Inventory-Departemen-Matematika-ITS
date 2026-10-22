<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('asset.category')
            ->latest('tanggal_servis')->latest('id')
            ->paginate(20);

        return view('maintenances.index', compact('maintenances'));
    }

    public function store(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'tanggal_servis' => 'required|date|before_or_equal:today',
            'jenis' => 'required|string|max:100',
            'teknisi' => 'nullable|string|max:255',
            'biaya' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);
        $data['biaya'] = $data['biaya'] ?? 0;

        $asset->maintenances()->create($data);

        return redirect()->route('assets.show', $asset)->with('ok', 'Riwayat servis dicatat.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $asset = $maintenance->asset;
        $maintenance->delete();

        return redirect()->route('assets.show', $asset)->with('ok', 'Riwayat servis dihapus.');
    }
}
