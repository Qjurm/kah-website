<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concert_score', function (Blueprint $table) {
            $table->foreignId('concert_id')->constrained('concerts')->cascadeOnDelete();
            $table->foreignId('score_id')->constrained('scores')->cascadeOnDelete();
            $table->primary(['concert_id', 'score_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concert_score');
    }
};
