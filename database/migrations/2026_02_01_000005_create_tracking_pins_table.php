<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tracking_pins')) {
            Schema::create('tracking_pins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_booking_id')->constrained()->onDelete('cascade');
                $table->string('pin', 6);
                $table->timestamp('expires_at');
                $table->timestamp('last_accessed_at')->nullable()->default(null);
                $table->integer('access_count')->default(0);  
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique('pin');
                $table->index('tour_booking_id');
                $table->index('expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_pins');
    }
};