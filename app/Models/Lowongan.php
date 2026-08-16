<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_posisi',
        'divisi',
        'deskripsi',
        'kualifikasi',
        'kuota',
        'status',
    ];

    /**
     * Relasi ke pendaftaran magang.
     */
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    /**
     * Relasi ke data magang aktif/alumni.
     */
    public function magangs(): HasMany
    {
        return $this->hasMany(Magang::class);
    }

    /**
     * Hitung jumlah pelamar yang disetujui (Approved).
     */
    public function getPelamarDisetujuiAttribute(): int
    {
        return $this->pendaftarans()->where('status', 'approved')->count();
    }

    /**
     * Hitung sisa kuota lowongan.
     */
    public function getKuotaTersisaAttribute(): int
    {
        $tersisa = $this->kuota - $this->pelamar_disetujui;
        return max(0, $tersisa);
    }

    /**
     * Cek apakah lowongan masih menerima pelamar.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'buka' && $this->kuota_tersisa > 0;
    }
}
