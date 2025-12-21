<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    // 1. TAMPILKAN FORM
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // 2. PROSES UPDATE
    public function update(Request $request)
    {
        $user = Auth::user(); // User yang sedang login

        // Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Unik kecuali punya sendiri
            'current_password' => 'nullable|required_with:password', // Wajib isi jika mau ganti pass
            'password' => 'nullable|min:6|confirmed', // 'confirmed' berarti harus cocok dengan field password_confirmation
        ]);

        // A. Update Info Dasar (Nama & Email)
        $user->name = $request->name;
        $user->email = $request->email;

        // B. Update Password (Jika diisi)
        if ($request->filled('password')) {
            // Cek Password Lama
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.']);
            }

            // Simpan Password Baru
            $user->password = Hash::make($request->password);
        }

        /** @var \App\Models\User $user */
        $user->save();

        return back()->with('success', 'Profil dan Password berhasil diperbarui!');
    }
}
