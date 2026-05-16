<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (Schema::hasColumn('posts', 'rating_avg')) {
                    $table->dropColumn('rating_avg');
                }
                if (Schema::hasColumn('posts', 'rating_count')) {
                    $table->dropColumn('rating_count');
                }
            });
        }

        Schema::dropIfExists('ratings');

        if (!Schema::hasTable('trek_ratings')) {
            Schema::create('trek_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('tour_booking_id')->constrained()->onDelete('cascade');
                $table->smallInteger('rating'); 
                $table->text('review')->nullable();
                $table->timestamps();

                $table->unique(['tour_package_id', 'user_id'], 'unique_trek_rating');
                $table->index(['tour_package_id', 'rating'], 'idx_package_rating');
                $table->index('user_id', 'idx_user');
            });
        }

        if (!Schema::hasTable('package_recommendation_scores')) {
            Schema::create('package_recommendation_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
                $table->decimal('preference_score', 5, 4)->default(0);
                $table->decimal('popularity_score', 5, 4)->default(0);
                $table->decimal('behavioral_score', 5, 4)->default(0);
                $table->decimal('final_score',      5, 4)->default(0);
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'tour_package_id']);
                $table->index(['user_id', 'final_score'], 'idx_user_score');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trek_ratings');
        Schema::dropIfExists('package_recommendation_scores');

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->decimal('rating_avg', 3, 2)->default(0.00);
                $table->integer('rating_count')->default(0); 
            });
        }
    }
};