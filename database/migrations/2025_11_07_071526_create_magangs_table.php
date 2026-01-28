<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->string('asal_kampus');
            $table->integer('periode_bulan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('karya')->nullable();
            $table->enum('status', ['belum_aktif', 'aktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magangs');
    }
};
