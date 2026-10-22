<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('barang')->orderBy('nama')->get();

        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        Kategori::create($request->validate([
            'nama' => 'required|string|max:100|unique:kategori_barang,nama',
            'umur_ekonomis_tahun' => 'nullable|integer|min:1|max:100',
        ]));

        return back()->with('ok', 'Kategori ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori_barang', 'nama')->ignore($kategori->id)],
            'umur_ekonomis_tahun' => 'nullable|integer|min:1|max:100',
        ]));

        return redirect()->route('kategori.index')->with('ok', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->barang()->exists()) {
            return back()->withErrors(['nama' => 'Kategori masih dipakai oleh barang, tidak bisa dihapus.']);
        }
        $kategori->delete();

        return back()->with('ok', 'Kategori dihapus.');
    }
}
