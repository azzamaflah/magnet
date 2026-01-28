<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/..._add_surat_kampus_to_pendaftarans_table.php
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Tambahkan kolom baru setelah surat_permohonan
            $table->string('surat_kampus')->nullable()->after('surat_permohonan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            //
        });
    }
};
