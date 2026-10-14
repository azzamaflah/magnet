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
        if (Schema::hasTable('pendaftarans') && !Schema::hasColumn('pendaftarans', 'user_id')) {
            Schema::table('pendaftarans', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('magangs') && !Schema::hasColumn('magangs', 'user_id')) {
            Schema::table('magangs', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pendaftarans') && Schema::hasColumn('pendaftarans', 'user_id')) {
            Schema::table('pendaftarans', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('magangs') && Schema::hasColumn('magangs', 'user_id')) {
            Schema::table('magangs', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
