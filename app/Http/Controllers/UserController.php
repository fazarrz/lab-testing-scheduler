<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        // Mengambil semua user dengan role 'engineer'
        $users = User::where('role', 'engineer')->get();

        return view('user.index', compact('users'));
    }


    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user baru

        $role = 'engineer';

        $user = new User([
            'name' => $request->input('name'),
            'role' => $role,
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')), // Enkripsi password dengan Hash
        ]);

        // Simpan user ke database
        $user->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Tampilkan halaman edit dengan data user
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Perbarui data user
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Simpan perubahan
        $user->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Hapus user dari database
        $user->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }

}
