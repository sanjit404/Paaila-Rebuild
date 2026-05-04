<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->string('identifier', 255);
            $table->unsignedTinyInteger('rating'); 
            $table->timestamps();
            
            $table->unique(['post_id', 'identifier'], 'unique_rating_per_user');
            
            $table->index('identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};