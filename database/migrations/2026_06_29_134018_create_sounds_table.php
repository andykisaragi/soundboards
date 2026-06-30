<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sounds', function (Blueprint $table) {
            $table->id();
            $table->string('freesound_id')->unique();  // Freesound's ID, unique so we don't duplicate
            $table->string('name');
            $table->string('username');
            $table->string('preview_url')->nullable();
            $table->string('waveform_url')->nullable();
            $table->json('raw_data')->nullable();       // Store the full API response for later use
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sounds');
    }
};
