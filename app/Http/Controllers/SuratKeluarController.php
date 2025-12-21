<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\LogSuratKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratKeluar::with(['pembuat', 'posisi'])->latest();

        // 1. Filter Hak Akses Dasar
        if ($user->role == 'staff') {
            $query->where('pembuat_id', $user->id);
        }

        // 2. Filter dari Dashboard (LOGIKA BARU)

        // A. Filter Status Saat Ini (Untuk Staf/Admin/Tugas Aktif)
        if ($request->filter == 'proses') {
            $query->whereIn('status_acc', ['pending_kasi', 'pending_kabid']);
        } elseif ($request->filter == 'acc') {
            $query->where('status_acc', 'acc');
        } elseif ($request->filter == 'revisi') {
            $query->where('status_acc', 'revisi');
        } elseif ($request->filter == 'ditolak') {
            $query->where('status_acc', 'ditolak');
        }

        // B. Filter History Kinerja (KHUSUS PIMPINAN)
        // Menampilkan surat yang PERNAH diproses oleh user yang login
        elseif ($request->filter == 'history_acc') {
            $query->whereHas('logs', function ($q) use ($user) {
                $q->where('from_user_id', $user->id)->where('aksi', 'acc');
            });
        } elseif ($request->filter == 'history_revisi') {
            $query->whereHas('logs', function ($q) use ($user) {
                $q->where('from_user_id', $user->id)
                    ->where('aksi', 'revisi')
                    ->where('catatan_revisi', 'NOT LIKE', '%DITOLAK PERMANEN%');
            });
        } elseif ($request->filter == 'history_ditolak') {
            $query->whereHas('logs', function ($q) use ($user) {
                $q->where('from_user_id', $user->id)
                    ->where(function ($sub) {
                        $sub->where('aksi', 'ditolak')
                            ->orWhere('catatan_revisi', 'LIKE', '%DITOLAK PERMANEN%');
                    });
            });
        }

        // 3. Search
        if ($request->has('search')) {
            $query->where('perihal', 'like', '%' . $request->search . '%');
        }

        $data = $query->get();
        return view('surat-keluar.index', compact('data'));
    }

    public function create()
    {
        return view('surat-keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_surat' => 'required|mimes:pdf,doc,docx|max:2048',
            'perihal' => 'required',
            'tanggal_surat' => 'required|date',
        ]);

        // Upload File
        $path = $request->file('file_surat')->store('surat-keluar', 'public');

        // Cari Kasi (Atasan Staff ini)
        $kasi = User::where('id', Auth::user()->parent_id)->first();

        // PENTING: Cek jika Kasi tidak ditemukan
        if (!$kasi) {
            return back()->with('error', 'Akun Anda belum memiliki atasan (Kasi). Harap hubungi Admin untuk setting akun.');
        }

        // Simpan Data Surat
        $surat = SuratKeluar::create([
            'nomor_surat' => null,
            'tanggal_surat' => $request->tanggal_surat,
            'perihal' => $request->perihal,
            'file_path' => $path,
            'pembuat_id' => Auth::id(),
            'posisi_saat_ini' => $kasi->id,
            'status_acc' => 'pending_kasi',
        ]);

        // Catat Log (INI BAGIAN YANG DIPERBAIKI)
        LogSuratKeluar::create([
            'surat_keluar_id' => $surat->id,
            'from_user_id'    => Auth::id(), // <--- DULU 'pengirim_id', SEKARANG 'from_user_id'
            'to_user_id'      => $kasi->id,  // <--- DULU 'penerima_id', SEKARANG 'to_user_id'
            'aksi'            => 'upload',
            'catatan_revisi'  => 'Draft baru dibuat.'
        ]);

        return redirect()->route('surat-keluar.index')->with('success', 'Draft surat berhasil dikirim ke Kasi!');
    }
    public function show($id)
    {
        $surat = SuratKeluar::with(['logs.pengirim', 'logs.penerima', 'pembuat', 'posisi'])->findOrFail($id);
        $user = Auth::user();

        // Cek Hak Akses
        $isAllowed = false;
        if ($user->role == 'admin') $isAllowed = true;
        else if ($surat->pembuat_id == $user->id) $isAllowed = true;
        else if ($surat->posisi_saat_ini == $user->id) $isAllowed = true;
        else if (in_array($user->role, ['kabid', 'kasi'])) $isAllowed = true;

        if (!$isAllowed) abort(403);

        $catatanRevisi = LogSuratKeluar::where('surat_keluar_id', $id)
            ->where('aksi', 'revisi')
            ->latest()->first();

        return view('surat-keluar.show', compact('surat', 'catatanRevisi'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratKeluar::findOrFail($id);
        $user = Auth::user();

        // --- A. JIKA STAFF UPLOAD REVISI ---
        if ($request->hasFile('file_surat')) {
            $path = $request->file('file_surat')->store('surat-keluar', 'public');

            $surat->file_path = $path;

            // Kembalikan ke Kasi (Atasan Langsung)
            $kasi = User::where('id', $surat->pembuat->parent_id)->first();

            $surat->posisi_saat_ini = $kasi->id;
            $surat->status_acc = 'pending_kasi';
            $surat->save();

            LogSuratKeluar::create([
                'surat_keluar_id' => $surat->id,
                'from_user_id'    => $user->id,   // <--- UBAH INI (Dulu pengirim_id)
                'to_user_id'      => $kasi->id,   // <--- UBAH INI (Dulu penerima_id)
                'aksi'            => 'resubmit',
                'catatan_revisi'  => 'File revisi telah diupload ulang.'
            ]);

            return back()->with('success', 'Revisi berhasil dikirim!');
        }

        // --- B. JIKA PIMPINAN MELAKUKAN VALIDASI ---

        // 1. AKSI: ACC (SETUJUI)
        if ($request->aksi == 'acc') {

            if ($user->role == 'kasi') {
                $kabid = User::where('role', 'kabid')->first();
                if (!$kabid) return back()->with('error', 'Data Kabid tidak ditemukan.');

                $surat->posisi_saat_ini = $kabid->id;
                $surat->status_acc = 'pending_kabid';
                $surat->save();

                LogSuratKeluar::create([
                    'surat_keluar_id' => $surat->id,
                    'from_user_id'    => $user->id,    // <--- UBAH INI
                    'to_user_id'      => $kabid->id,   // <--- UBAH INI
                    'aksi'            => 'acc',
                    'catatan_revisi'  => $request->catatan
                ]);
            } elseif ($user->role == 'kabid') {
                $surat->posisi_saat_ini = null;
                $surat->status_acc = 'acc';
                $surat->nomor_surat = '522/' . rand(100, 999) . '/DISHUT/' . date('Y');
                $surat->save();

                LogSuratKeluar::create([
                    'surat_keluar_id' => $surat->id,
                    'from_user_id'    => $user->id,           // <--- UBAH INI
                    'to_user_id'      => $surat->pembuat_id,  // <--- UBAH INI
                    'aksi'            => 'acc',
                    'catatan_revisi'  => 'Surat disetujui dan diterbitkan.'
                ]);
            }
            return back()->with('success', 'Surat berhasil disetujui!');
        }

        // 2. AKSI: REVISI (KEMBALIKAN)
        elseif ($request->aksi == 'revisi') {

            $surat->status_acc = 'revisi';
            $surat->posisi_saat_ini = $surat->pembuat_id;
            $surat->save();

            LogSuratKeluar::create([
                'surat_keluar_id' => $surat->id,
                'from_user_id'    => $user->id,           // <--- UBAH INI
                'to_user_id'      => $surat->pembuat_id,  // <--- UBAH INI
                'aksi'            => 'revisi',
                'catatan_revisi'  => $request->catatan
            ]);

            return back()->with('success', 'Surat dikembalikan untuk revisi.');
        }

        // 3. AKSI: DITOLAK (STOP TOTAL)
        elseif ($request->aksi == 'ditolak') {

            $surat->status_acc = 'ditolak';
            $surat->posisi_saat_ini = $surat->pembuat_id;
            $surat->save();

            LogSuratKeluar::create([
                'surat_keluar_id' => $surat->id,
                'from_user_id'    => $user->id,           // <--- UBAH INI
                'to_user_id'      => $surat->pembuat_id,  // <--- UBAH INI
                'aksi'            => 'revisi', // Tipe revisi agar log berwarna merah/danger
                'catatan_revisi'  => 'DITOLAK PERMANEN: ' . ($request->catatan ?? 'Tidak memenuhi syarat.')
            ]);

            return back()->with('success', 'Surat telah DITOLAK secara permanen.');
        }
    }
}
