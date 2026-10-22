<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('assets')->orderBy('nama')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama',
            'interval_default_bulan' => 'nullable|integer|min:1|max:120',
        ]);
        Category::create($data);

        return back()->with('ok', 'Kategori ditambahkan.');
    }

    public function destroy(Category $category)
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['nama' => 'Kategori masih dipakai oleh aset, tidak bisa dihapus.']);
        }
        $category->delete();

        return back()->with('ok', 'Kategori dihapus.');
    }
}
