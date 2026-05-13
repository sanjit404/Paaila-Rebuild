<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('trek_types')->nullable();
            $table->enum('difficulty', ['easy', 'moderate', 'hard', 'any'])->default('any');
            $table->enum('duration', ['1-3', '4-7', '8-14', 'any'])->default('any');
            $table->enum('budget', ['budget', 'mid', 'premium', 'any'])->default('any');
            $table->enum('group_size', ['solo', 'couple', 'family', 'group', 'any'])->default('any');
            $table->json('preferred_seasons')->nullable();
            $table->boolean('preferences_set')->default(false);
            $table->timestamps();
            $table->unique('user_id');
            $table->index(['difficulty', 'budget']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
