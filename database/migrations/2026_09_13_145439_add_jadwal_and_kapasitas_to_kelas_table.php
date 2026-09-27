<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'jadwal')) {
                $table->string('jadwal')->nullable();
            }
            if (!Schema::hasColumn('kelas', 'kapasitas')) {
                $table->unsignedInteger('kapasitas')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('kelas', 'jadwal')) $columns[] = 'jadwal';
            if (Schema::hasColumn('kelas', 'kapasitas')) $columns[] = 'kapasitas';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};