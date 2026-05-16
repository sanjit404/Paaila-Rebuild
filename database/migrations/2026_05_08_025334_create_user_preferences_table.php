<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('trek_types')->nullable();
            $table->string('difficulty')->default('any');  
            $table->string('duration')->default('any');    
            $table->string('budget')->default('any');      
            $table->string('group_size')->default('any');  
            $table->json('preferred_seasons')->nullable();
            $table->boolean('preferences_set')->default(false);
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['difficulty', 'budget']);
        });

        DB::statement("ALTER TABLE user_preferences ADD CONSTRAINT chk_difficulty CHECK (difficulty IN ('easy', 'moderate', 'hard', 'any'))");
        DB::statement("ALTER TABLE user_preferences ADD CONSTRAINT chk_duration   CHECK (duration   IN ('1-3', '4-7', '8-14', 'any'))");
        DB::statement("ALTER TABLE user_preferences ADD CONSTRAINT chk_budget     CHECK (budget     IN ('budget', 'mid', 'premium', 'any'))");
        DB::statement("ALTER TABLE user_preferences ADD CONSTRAINT chk_group_size CHECK (group_size IN ('solo', 'couple', 'family', 'group', 'any'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};