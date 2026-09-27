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
        Schema::table('tugas', function (Blueprint $table) {
            if (!Schema::hasColumn('tugas', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('tugas', 'tenggat_waktu')) {
                $table->dateTime('tenggat_waktu')->nullable();
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'file_path')) {
                $table->string('file_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (Schema::hasColumn('tugas', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('tugas', 'tenggat_waktu')) {
                $table->dropColumn('tenggat_waktu');
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};