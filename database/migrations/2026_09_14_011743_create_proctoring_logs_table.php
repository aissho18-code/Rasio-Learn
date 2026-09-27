<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proctoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('exam_id')->nullable();
            $table->string('type');
            $table->text('detail');
            $table->enum('severity', ['info', 'warn', 'critical'])->default('warn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proctoring_logs');
    }
};