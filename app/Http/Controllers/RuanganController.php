<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::withCount('barang')->orderBy('nama')->get();

        return view('ruangan.index', compact('ruangan'));
    }

    public function store(Request $request)
    {
        Ruangan::create($request->validate([
            'nama' => 'required|string|max:100|unique:ruangan,nama',
            'keterangan' => 'nullable|string|max:255',
        ]));

        return back()->with('ok', 'Ruangan ditambahkan.');
    }

    public function edit(Ruangan $ruangan)
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $ruangan->update($request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('ruangan', 'nama')->ignore($ruangan->id)],
            'keterangan' => 'nullable|string|max:255',
        ]));

        return redirect()->route('ruangan.index')->with('ok', 'Ruangan diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        $dipakai = Barang::where('ruangan_id', $ruangan->id)->exists()
            || Mutasi::where('ruangan_asal_id', $ruangan->id)->orWhere('ruangan_tujuan_id', $ruangan->id)->exists();

        if ($dipakai) {
            return back()->withErrors(['nama' => 'Ruangan sudah dipakai di data barang atau riwayat perpindahan, tidak bisa dihapus.']);
        }
        $ruangan->delete();

        return back()->with('ok', 'Ruangan dihapus.');
    }
}
