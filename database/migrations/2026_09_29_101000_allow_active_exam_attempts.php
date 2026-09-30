<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('exam_submissions')
            ->whereNull('submitted_at')
            ->update(['submitted_at' => now()]);

        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable(false)->change();
        });
    }
};
