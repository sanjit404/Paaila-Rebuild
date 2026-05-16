<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('type')->default('news'); 
                $table->string('image')->nullable();
                $table->integer('likes_count')->default(0);        
                $table->decimal('rating_avg', 3, 2)->default(0.00);
                $table->integer('rating_count')->default(0);       
                $table->boolean('is_highlighted')->default(false);
                $table->foreignId('trek_id')->nullable()->constrained('tour_packages')->onDelete('cascade');
                $table->timestamps();

                $table->index('type');
                $table->index('is_highlighted');
                $table->index(['likes_count', 'created_at']);
            });

            DB::statement("ALTER TABLE posts ADD CONSTRAINT chk_post_type CHECK (type IN ('news', 'offer', 'trek'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};