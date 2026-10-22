<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('categories', 'nama')->ignore($category->id)],
            'interval_default_bulan' => 'nullable|integer|min:1|max:120',
        ]);
        $category->update($data);

        return redirect()->route('categories.index')->with('ok', 'Kategori diperbarui.');
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
