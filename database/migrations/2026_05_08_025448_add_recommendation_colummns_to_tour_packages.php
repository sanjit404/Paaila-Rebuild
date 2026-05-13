<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'trek_type')) {
                $table->string('trek_type', 50)->nullable()->after('description');
                $table->index('trek_type');
            }
            if (!Schema::hasColumn('tour_packages', 'tags')) {
                $table->json('tags')->nullable()->after('trek_type');
            }
            if (!Schema::hasColumn('tour_packages', 'season')) {
                $table->json('season')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('tour_packages', 'region')) {
                $table->string('region', 100)->nullable()->after('season');
            }
            if (!Schema::hasColumn('tour_packages', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('region');
            }
            if (!Schema::hasColumn('tour_packages', 'bookings_count')) {
                $table->unsignedInteger('bookings_count')->default(0)->after('views_count');
            }
            if (!Schema::hasColumn('tour_packages', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->default(0.00)->after('bookings_count');
            }
            if (!Schema::hasColumn('tour_packages', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropIndex(['trek_type']);
            $table->dropColumn([
                'trek_type', 'tags', 'season', 'region',
                'views_count', 'bookings_count',
                'rating_avg', 'rating_count',
            ]);
        });
    }
};
