<?php
// app/Models/Pendaftaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_pendaftar',
        'email', 
        'surat_permohonan',
        'surat_kampus',
        'pas_foto',
        'asal_kampus',
        'prodi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'remarks',
        'konfirmasi_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}