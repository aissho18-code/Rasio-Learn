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
            if (!Schema::hasColumn('tugas', 'pekan')) {
                $table->string('pekan')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('tugas', 'status')) {
                $table->string('status')->default('aktif')->after('pekan'); // 'aktif' (unlock) atau 'terkunci' (lock)
            }
            if (!Schema::hasColumn('tugas', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('status');
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
