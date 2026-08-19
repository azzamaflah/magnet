<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            if (!Schema::hasColumn('magangs', 'email')) {
                $table->string('email')->nullable()->after('nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            if (Schema::hasColumn('magangs', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
