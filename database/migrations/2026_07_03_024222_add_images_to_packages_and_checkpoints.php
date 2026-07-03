<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('images');
        });
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};