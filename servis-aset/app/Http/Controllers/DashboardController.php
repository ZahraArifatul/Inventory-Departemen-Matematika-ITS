<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;

class DashboardController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'lastMaintenance'])->where('status', 'aktif')->get();

        $terlambat = $assets->where('status_servis', 'terlambat')->sortBy('sisa_hari');
        $segera    = $assets->where('status_servis', 'segera')->sortBy('sisa_hari');

        $stat = [
            'total'     => $assets->count(),
            'terlambat' => $terlambat->count(),
            'segera'    => $segera->count(),
            'aman'      => $assets->where('status_servis', 'aman')->count(),
        ];

        $riwayat = Maintenance::with('asset')->latest('tanggal_servis')->latest('id')->take(8)->get();

        return view('dashboard', compact('stat', 'terlambat', 'segera', 'riwayat'));
    }
}
