<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/..._add_prodi_to_pendaftarans_table.php
// database/migrations/..._add_prodi_to_pendaftarans_table.php
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'asal_kampus'
            $table->string('prodi')->nullable()->after('asal_kampus');
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
