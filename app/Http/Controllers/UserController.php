<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. DAFTAR SEMUA USER
    public function index()
    {
        // Tampilkan semua user kecuali Admin sendiri (biar ga kehapus ga sengaja)
        $users = User::where('role', '!=', 'admin')->latest()->get();
        return view('users.index', compact('users'));
    }

    // 2. FORM TAMBAH USER
    public function create()
    {
        // Kita butuh data atasan untuk dropdown
        // Ambil semua Kabid (untuk calon atasan Kasi)
        $kabids = User::where('role', 'kabid')->get();

        // Ambil semua Kasi (untuk calon atasan Staf)
        $kasis = User::where('role', 'kasi')->get();

        return view('users.create', compact('kabids', 'kasis'));
    }

    // 3. SIMPAN USER BARU
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'jabatan' => 'required',
            // parent_id boleh null (jika Kabid)
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'parent_id' => $request->parent_id, // ID Atasan yang dipilih
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // 4. HAPUS USER
    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('users.index')->with('success', 'User dihapus!');
    }
}
