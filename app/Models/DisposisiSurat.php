<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiSurat extends Model
{
    use HasFactory;

    protected $table = 'disposisi_surat';

    protected $fillable = [
        'surat_masuk_id',
        'pengirim_id',
        'penerima_id',
        'instruksi',
        'is_read',
        'read_at',
        'status',
    ];

    // Milik surat yang mana?
    public function surat()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    // Siapa pengirim disposisi? (User)
    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    // Siapa penerima disposisi? (User)
    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
