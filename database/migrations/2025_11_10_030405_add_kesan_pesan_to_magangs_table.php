<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->text('kesan')->nullable()->after('tiktok');
            $table->text('pesan')->nullable()->after('kesan');
        });
    }

    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn(['kesan', 'pesan']);
        });
    }
};