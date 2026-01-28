<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Menentukan nama tabel (opsional jika nama tabelnya 'settings')
    protected $table = 'settings';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'key',
        'value',
        'type',
    ];
}
