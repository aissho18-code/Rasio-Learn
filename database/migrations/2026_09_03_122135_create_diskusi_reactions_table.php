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
        Schema::create('diskusi_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diskusi_id')->constrained('diskusi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'like' atau 'dislike'
            $table->timestamps();
            $table->unique(['diskusi_id', 'user_id']); // 1 user hanya bisa 1 reaction per topik
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskusi_reactions');
    }
};
