<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tour_bookings DROP CONSTRAINT IF EXISTS chk_status");
        DB::statement("ALTER TABLE tour_bookings ADD CONSTRAINT chk_status CHECK (status IN ('pending', 'confirmed', 'active', 'completed', 'cancelled'))");

        Schema::table('tour_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }

            if (!Schema::hasColumn('tour_bookings', 'admin_verified')) {
                $table->boolean('admin_verified')->default(false);
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
        DB::statement("ALTER TABLE tour_bookings DROP CONSTRAINT IF EXISTS chk_status");
    }
};