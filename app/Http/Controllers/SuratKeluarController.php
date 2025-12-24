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
        $query = SuratKeluar::with(['posisi', 'pembuat']);

        // Filter berdasarkan pencarian perihal
        if ($request->filled('search')) {
            $query->where('perihal', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_surat', $request->tahun);
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_surat', $request->bulan);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan status
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'proses':
                    $query->where('status_acc', 'proses');
                    break;
                case 'acc':
                    $query->where('status_acc', 'acc');
                    break;
                case 'revisi':
                    $query->where('status_acc', 'revisi');
                    break;
                case 'ditolak':
                    $query->where('status_acc', 'ditolak');
                    break;

                // History kinerja untuk kabid/kasi
                case 'history_acc':
                    $query->where('status_acc', 'acc')
                        ->where('posisi_terakhir_id', Auth::id());
                    break;
                case 'history_revisi':
                    $query->where('status_acc', 'revisi')
                        ->where('posisi_terakhir_id', Auth::id());
                    break;
                case 'history_ditolak':
                    $query->where('status_acc', 'ditolak')
                        ->where('posisi_terakhir_id', Auth::id());
                    break;
            }
        }

        // Filter berdasarkan role user
        $user = Auth::user();

        if ($user->role == 'staff') {
            // Staff hanya melihat surat yang dia buat
            $query->where('pembuat_id', $user->id);
        } elseif (in_array($user->role, ['kabid', 'kasi'])) {
            // Kabid/Kasi melihat surat yang ada di posisi mereka atau yang pernah mereka proses
            $query->where(function ($q) use ($user) {
                $q->where('posisi_saat_ini', $user->id)
                    ->orWhere('pembuat_id', $user->id)
                    ->orWhere('posisi_terakhir_id', $user->id);
            });
        }
        // Untuk role admin/superadmin, tidak ada filter tambahan (melihat semua)

        // Ambil list kategori unik untuk dropdown filter
        $kategoris = SuratKeluar::select('kategori')
            ->distinct()
            ->whereNotNull('kategori')
            ->orderBy('kategori')
            ->pluck('kategori');

        // Urutkan berdasarkan tanggal terbaru dan paginate
        $data = $query->orderBy('tanggal_surat', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('surat-keluar.index', compact('data', 'kategoris'));
    }

    public function create()
    {
        return view('surat-keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_surat' => 'required|mimes:pdf,doc,docx|max:2048',
            'kategori' => 'required',
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
            'kategori' => $request->kategori,
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
