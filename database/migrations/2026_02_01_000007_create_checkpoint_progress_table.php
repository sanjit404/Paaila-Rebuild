<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('checkpoint_progress')) {
            Schema::create('checkpoint_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_booking_id')->constrained()->onDelete('cascade');
                $table->foreignId('checkpoint_id')->constrained()->onDelete('cascade');
                $table->timestamp('reached_at')->nullable();
                $table->boolean('facts_viewed')->default(false);
                $table->decimal('distance_from_checkpoint', 10, 2)->nullable(); // Real-time distance in meters
                $table->timestamps();
                
                // Ensure one progress record per checkpoint per booking
                $table->unique(['tour_booking_id', 'checkpoint_id'], 'unique_progress');
                $table->index('tour_booking_id');
                $table->index('checkpoint_id');
                $table->index('reached_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_progress');
    }
};
