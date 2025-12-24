<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk'; // Menentukan nama tabel

    protected $fillable = [
        'nomor_surat',
        'kategori',
        'tanggal_surat',
        'pengirim',
        'perihal',
        'file_path',
        'status_akhir',
    ];

    // Relasi: Satu surat bisa memiliki banyak riwayat disposisi
    public function disposisi()
    {
        return $this->hasMany(DisposisiSurat::class, 'surat_masuk_id');
    }
}
