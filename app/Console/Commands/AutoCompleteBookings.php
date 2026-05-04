<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookingValidationService;

class AutoCompleteBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:auto-complete';

    /**
     * The console command description.
     */
    protected $description = 'Automatically complete active bookings that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for expired active bookings...');

        $completedCount = BookingValidationService::autoCompleteExpiredBookings();

        if ($completedCount > 0) {
            $this->info("✅ Auto-completed {$completedCount} booking(s)");
        } else {
            $this->info('✅ No bookings needed auto-completion');
        }

        return Command::SUCCESS;
    }
}