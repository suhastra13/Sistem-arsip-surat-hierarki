<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'pembuat_id',
        'kategori',
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'file_path',
        'status_acc',
        'posisi_saat_ini',
    ];

    // Siapa yang buat?
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    // Dimana posisi surat sekarang?
    public function posisi()
    {
        return $this->belongsTo(User::class, 'posisi_saat_ini');
    }

    // Riwayat revisi/acc
    public function logs()
    {
        return $this->hasMany(LogSuratKeluar::class, 'surat_keluar_id');
    }
}
