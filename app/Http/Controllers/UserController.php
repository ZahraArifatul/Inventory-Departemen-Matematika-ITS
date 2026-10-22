<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Perbaikan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('users.create', ['user' => new User(['role' => 'teknisi', 'aktif' => true])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('users.index')->with('ok', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);
        $aktif = $request->boolean('aktif');

        if ($user->id === $request->user()->id && ($data['role'] !== 'admin' || !$aktif)) {
            return back()->withErrors(['role' => 'Anda tidak bisa menurunkan peran atau menonaktifkan akun Anda sendiri.'])->withInput();
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'], 'aktif' => $aktif]);
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('users.index')->with('ok', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['name' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }

        if (Mutasi::where('petugas_id', $user->id)->exists() || Perbaikan::where('petugas_id', $user->id)->exists()) {
            return back()->withErrors(['name' => 'Pengguna ini tercatat di riwayat barang, jadi tidak bisa dihapus. Nonaktifkan saja akunnya.']);
        }

        $user->delete();

        return back()->with('ok', 'Pengguna dihapus.');
    }
}
