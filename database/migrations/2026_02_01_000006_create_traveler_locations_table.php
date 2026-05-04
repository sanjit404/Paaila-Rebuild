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
                $table->decimal('latitude', 10, 8); // Precision for accurate GPS
                $table->decimal('longitude', 11, 8); // Precision for accurate GPS
                $table->decimal('accuracy', 8, 2)->nullable(); // GPS accuracy in meters
                $table->decimal('speed', 8, 2)->nullable(); // Speed in m/s
                $table->decimal('altitude', 8, 2)->nullable(); // Altitude in meters
                $table->decimal('heading', 6, 2)->nullable(); // Direction in degrees (0-360)
                $table->string('battery_level')->nullable(); // Battery percentage
                $table->timestamps();
                
                // Indexes for performance
                $table->index('tour_booking_id');
                $table->index('created_at'); // For time-based queries
                $table->index(['tour_booking_id', 'created_at']); // Composite for latest location
                $table->index(['latitude', 'longitude']); // For geospatial queries
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('traveler_locations');
    }
};
