<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed', 
                'active',
                'completed',
                'cancelled'
            ])->default('pending')->change();
            
            if (!Schema::hasColumn('tour_bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('started_at');
            }
            
            if (!Schema::hasColumn('tour_bookings', 'admin_verified')) {
                $table->boolean('admin_verified')->default(false)->after('status');
            }
            
            $table->index(['user_id', 'status'], 'user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropIndex('user_status_idx');
            $table->dropColumn(['confirmed_at', 'admin_verified']);
        });
    }
};