<?php
// database/migrations/xxxx_add_social_media_to_magangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('magangs', function (Blueprint $table) {
            // Hapus after() karena kolom tidak ada
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
        });
    }

    public function down()
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'instagram', 'tiktok']);
        });
    }
};
