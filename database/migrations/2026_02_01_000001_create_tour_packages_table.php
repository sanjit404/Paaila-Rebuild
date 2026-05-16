<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->string('difficulty_level')->default('easy'); 
            $table->integer('max_participants')->default(20);    
            $table->string('image')->nullable();
            $table->string('start_location_name');
            $table->double('start_lat');  
            $table->double('start_lng');
            $table->string('end_location_name');
            $table->double('end_lat');
            $table->double('end_lng');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};