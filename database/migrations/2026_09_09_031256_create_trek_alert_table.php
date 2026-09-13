<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trek_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('checkpoint_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('severity')->default('warning'); // info, warning, danger
            $table->string('status')->default('active');    // active, monitoring, resolved
            $table->timestamps();

            $table->index(['tour_package_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trek_alerts');
    }
};