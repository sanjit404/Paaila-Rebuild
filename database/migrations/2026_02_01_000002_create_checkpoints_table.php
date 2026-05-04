<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('checkpoints')) {
            Schema::create('checkpoints', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->unsignedInteger('estimated_time_from_previous')->default(0);
                $table->unsignedInteger('order')->default(0);
                $table->unsignedInteger('radius')->default(50); // meters
                $table->timestamps();
                
                $table->index(['tour_package_id', 'order']);
                $table->index(['latitude', 'longitude']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
