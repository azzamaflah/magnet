<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom 'value' di tabel settings dari VARCHAR(255) menjadi TEXT
     * agar bisa menyimpan JSON array panjang (seperti daftar divisi).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    /**
     * Kembalikan ke VARCHAR(255) jika rollback.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('value', 255)->nullable()->change();
        });
    }
};
