<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->enum('type', ['news', 'offer', 'trek'])->default('news');
                $table->string('image')->nullable();
                $table->unsignedInteger('likes_count')->default(0);
                $table->decimal('rating_avg', 3, 2)->default(0.00);
                $table->unsignedInteger('rating_count')->default(0);
                $table->boolean('is_highlighted')->default(false);
                $table->foreignId('trek_id')->nullable()->constrained('tour_packages')->onDelete('cascade');
                $table->timestamps();
                
                $table->index('type');
                $table->index('is_highlighted');
                $table->index(['likes_count', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};