<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/..._add_konfirmasi_at_...
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Tambahkan kolom timestamp, bisa null
            $table->timestamp('konfirmasi_at')->nullable()->after('remarks');
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
