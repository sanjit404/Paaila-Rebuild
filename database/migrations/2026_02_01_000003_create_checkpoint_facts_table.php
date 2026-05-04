<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('checkpoint_facts')) {
            Schema::create('checkpoint_facts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('checkpoint_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('content');
                $table->string('type')->default('info'); // historical, cultural, natural, safety, tip, info
                $table->string('icon_class')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
                
                $table->index(['checkpoint_id', 'order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_facts');
    }
};
