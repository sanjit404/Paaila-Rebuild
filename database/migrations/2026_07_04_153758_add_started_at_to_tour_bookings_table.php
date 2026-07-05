<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('tour_bookings', function (Blueprint $table) {
        $table->timestamp('start_reached_at')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('tour_bookings', function (Blueprint $table) {
        $table->dropColumn('start_reached_at');
    });
}
};
