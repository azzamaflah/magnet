<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->foreignId('lowongan_id')->nullable()->after('user_id')->constrained('lowongans')->nullOnDelete();
        });

        Schema::table('magangs', function (Blueprint $table) {
            $table->foreignId('lowongan_id')->nullable()->after('user_id')->constrained('lowongans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropForeign(['lowongan_id']);
            $table->dropColumn('lowongan_id');
        });

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropForeign(['lowongan_id']);
            $table->dropColumn('lowongan_id');
        });
    }
};
