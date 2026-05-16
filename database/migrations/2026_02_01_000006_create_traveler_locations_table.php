<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('traveler_locations')) {
            Schema::create('traveler_locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_booking_id')->constrained()->onDelete('cascade');
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->decimal('accuracy', 8, 2)->nullable();
                $table->decimal('speed', 8, 2)->nullable();
                $table->decimal('altitude', 8, 2)->nullable();
                $table->decimal('heading', 6, 2)->nullable();
                $table->string('battery_level')->nullable();
                $table->timestamps();

                $table->index('tour_booking_id');
                $table->index('created_at');
                $table->index(['tour_booking_id', 'created_at']);
                $table->index(['latitude', 'longitude']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('traveler_locations');
    }
};