<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lkpd_questions', function (Blueprint $table) {
            $table->text('pembahasan')->nullable()->after('rubrik_jawaban');
        });

        Schema::table('lkpd_submissions', function (Blueprint $table) {
            $table->json('hasil_penilaian')->nullable()->after('jawaban');
        });
    }

    public function down(): void
    {
        Schema::table('lkpd_submissions', function (Blueprint $table) {
            $table->dropColumn('hasil_penilaian');
        });

        Schema::table('lkpd_questions', function (Blueprint $table) {
            $table->dropColumn('pembahasan');
        });
    }
};
