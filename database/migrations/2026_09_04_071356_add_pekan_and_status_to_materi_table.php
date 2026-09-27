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
        Schema::table('materi', function (Blueprint $table) {
            if (!Schema::hasColumn('materi', 'pekan')) {
                $table->string('pekan')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('materi', 'status')) {
                $table->string('status')->default('aktif')->after('file_path'); // 'aktif' (unlock) atau 'terkunci' (lock)
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            //
        });
    }
};
