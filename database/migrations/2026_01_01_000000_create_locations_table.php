<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->text('description')->nullable();
            $t->double('lat');    
            $t->double('lng');
            $t->string('type')->default('marker'); 
            $t->string('icon')->nullable();
            $t->string('color')->default('#3388ff');
            $t->boolean('is_public')->default(false);
            $t->timestamps();
        });

        Schema::create('location_history', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->double('lat');
            $t->double('lng');
            $t->double('accuracy')->nullable();
            $t->double('speed')->nullable();
            $t->double('altitude')->nullable();
            $t->double('heading')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'created_at']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->double('lat')->nullable();
            $t->double('lng')->nullable();
            $t->timestamp('last_location_update')->nullable();
            $t->boolean('sharing_enabled')->default(true);
            $t->string('map_style')->default('streets');
        });

        Schema::create('geofences', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->double('center_lat');
            $t->double('center_lng');
            $t->integer('radius'); 
            $t->boolean('notify_entry')->default(true);
            $t->boolean('notify_exit')->default(true);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geofences');
        Schema::dropIfExists('location_history');
        Schema::dropIfExists('locations');
    }
};