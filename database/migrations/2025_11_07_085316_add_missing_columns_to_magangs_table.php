<?php
// database/migrations/xxxx_add_missing_columns_to_magangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('magangs', function (Blueprint $table) {
            // Cek kolom mana yang belum ada
            if (!Schema::hasColumn('magangs', 'link_pekerjaan')) {
                $table->text('link_pekerjaan')->nullable();
            }
            if (!Schema::hasColumn('magangs', 'periode_bulan')) {
                $table->integer('periode_bulan')->nullable();
            }
            if (!Schema::hasColumn('magangs', 'status')) {
                $table->string('status')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn(['link_pekerjaan', 'periode_bulan', 'status']);
        });
    }
};
