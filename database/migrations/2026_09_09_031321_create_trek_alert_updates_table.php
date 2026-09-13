<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trek_alert_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trek_alert_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->timestamps();

            $table->index('trek_alert_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trek_alert_updates');
    }
};