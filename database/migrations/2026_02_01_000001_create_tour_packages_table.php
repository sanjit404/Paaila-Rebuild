<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->string('difficulty_level')->default('easy'); // easy, moderate, hard
            $table->integer('max_participants')->default(20);
            $table->string('image')->nullable();
            $table->string('start_location_name');
            $table->double('start_lat', 10, 7);
            $table->double('start_lng', 10, 7);
            $table->string('end_location_name');
            $table->double('end_lat', 10, 7);
            $table->double('end_lng', 10, 7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tour_packages');
    }
};
