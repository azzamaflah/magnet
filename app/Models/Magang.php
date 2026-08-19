<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Magang extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lowongan_id',
        'nama',
        'email',
        'foto',
        'asal_kampus',
        'prodi',
        'tanggal_mulai',
        'tanggal_selesai',
        'whatsapp',
        'instagram',
        'tiktok',
        'kesan',
        'pesan',
        'link_pekerjaan',
        'periode_bulan',
        'status',
        'is_hidden',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_hidden'       => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($magang) {
            if ($magang->tanggal_mulai && $magang->tanggal_selesai) {
                $mulai = Carbon::parse($magang->tanggal_mulai);
                $selesai = Carbon::parse($magang->tanggal_selesai);

                $periode = $mulai->diffInMonths($selesai);
                if ($selesai->day > $mulai->day) {
                    $periode += 1;
                }
                if ($periode == 0 && $mulai->diffInDays($selesai) > 0) {
                    $periode = 1;
                }
                $magang->periode_bulan = $periode;

                $now = Carbon::now();
                if ($now->lt($mulai)) {
                    $magang->status = 'belum_aktif';
                } elseif ($now->between($mulai, $selesai)) {
                    $magang->status = 'aktif';
                } else {
                    $magang->status = 'selesai';
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->nama);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->nama, 0, 2));
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }
}