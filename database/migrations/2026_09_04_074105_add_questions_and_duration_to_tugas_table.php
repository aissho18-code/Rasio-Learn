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
            if (!Schema::hasColumn('tugas', 'questions')) {
                $table->json('questions')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('tugas', 'durasi_menit')) {
                $table->integer('durasi_menit')->default(30)->after('questions');
            }
            if (!Schema::hasColumn('tugas', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('durasi_menit');
            }
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            //
        });
    }
};
