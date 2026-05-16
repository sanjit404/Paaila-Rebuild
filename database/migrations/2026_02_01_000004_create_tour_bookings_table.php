<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_bookings')) {
            Schema::create('tour_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
                $table->string('booking_number', 20)->unique();
                $table->date('tour_date');
                $table->integer('participants')->default(1);         
                $table->decimal('total_amount', 10, 2);
                $table->string('payment_method')->nullable();
                $table->string('status')->default('pending');

                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();

                $table->timestamps();

                $table->index(['user_id', 'status'], 'idx_user_status');
                $table->index('status', 'idx_status');
                $table->index('tour_date', 'idx_tour_date');
                $table->index('booking_number', 'idx_booking_number');
            });

            DB::statement("ALTER TABLE tour_bookings ADD CONSTRAINT chk_payment_method CHECK (payment_method IN ('esewa', 'khalti', 'stripe'))");
            DB::statement("ALTER TABLE tour_bookings ADD CONSTRAINT chk_status CHECK (status IN ('pending', 'confirmed', 'active', 'completed', 'cancelled'))");

        } else {
            Schema::table('tour_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('tour_bookings', 'booking_number')) {
                    $table->string('booking_number', 20)->unique()->after('id');
                }
                if (!Schema::hasColumn('tour_bookings', 'confirmed_at')) {
                    $table->timestamp('confirmed_at')->nullable();
                }
                if (!Schema::hasColumn('tour_bookings', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable();
                }
                if (!Schema::hasColumn('tour_bookings', 'started_at')) {
                    $table->timestamp('started_at')->nullable();
                }
                if (!Schema::hasColumn('tour_bookings', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable();
                }
                if (!Schema::hasColumn('tour_bookings', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
            });

            DB::statement("UPDATE tour_bookings SET status = 'confirmed' WHERE status = 'paid'");

            DB::statement("ALTER TABLE tour_bookings DROP CONSTRAINT IF EXISTS chk_status");
            DB::statement("ALTER TABLE tour_bookings ADD CONSTRAINT chk_status CHECK (status IN ('pending', 'confirmed', 'active', 'completed', 'cancelled'))");

            $bookings = DB::table('tour_bookings')->whereNull('booking_number')->get();
            foreach ($bookings as $booking) {
                DB::table('tour_bookings')
                    ->where('id', $booking->id)
                    ->update(['booking_number' => 'TRK' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)]);
            }

            try {
                Schema::table('tour_bookings', function (Blueprint $table) {
                    $table->index(['user_id', 'status'], 'idx_user_status');
                    $table->index('status', 'idx_status');
                    $table->index('tour_date', 'idx_tour_date');
                    $table->index('booking_number', 'idx_booking_number');
                });
            } catch (\Exception $e) {
     
            }
        }
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropIndex('idx_user_status');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_tour_date');
            $table->dropIndex('idx_booking_number');
        });
        DB::statement("ALTER TABLE tour_bookings DROP CONSTRAINT IF EXISTS chk_payment_method");
        DB::statement("ALTER TABLE tour_bookings DROP CONSTRAINT IF EXISTS chk_status");
    }
};