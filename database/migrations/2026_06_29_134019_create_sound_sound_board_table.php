<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sound_sound_board', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sound_board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sound_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);  // Optional: track ordering within a board
            $table->timestamps();

            // Prevent the same sound from being added twice to the same board
            $table->unique(['sound_board_id', 'sound_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sound_sound_board');
    }
};
