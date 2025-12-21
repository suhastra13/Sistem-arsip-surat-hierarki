<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'log_surat_keluar';

    protected $fillable = [
        'surat_keluar_id',
        'from_user_id',
        'to_user_id',
        'catatan_revisi',
        'aksi',
        'is_read',
        'read_at',
    ];

    public function surat()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
