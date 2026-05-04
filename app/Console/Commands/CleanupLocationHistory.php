<?php

namespace App\Console\Commands;

use App\Models\LocationHistory;
use Illuminate\Console\Command;

class CleanupLocationHistory extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tracking:cleanup
                          {--days=30 : Number of days to keep}
                          {--dry-run : Show what would be deleted without deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old location history records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');

        $cutoffDate = now()->subDays($days);

        $query = LocationHistory::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No old records to clean up.');
            return 0;
        }

        if ($dryRun) {
            $this->warn("DRY RUN: Would delete {$count} records older than {$days} days.");
            
            // Show sample records
            $sample = $query->limit(5)->get(['id', 'user_id', 'created_at']);
            $this->table(
                ['ID', 'User ID', 'Created At'],
                $sample->map(fn($record) => [
                    $record->id,
                    $record->user_id,
                    $record->created_at->format('Y-m-d H:i:s')
                ])
            );
            
            return 0;
        }

        $this->warn("Deleting {$count} location history records older than {$days} days...");
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $deleted = 0;
        $query->chunk(1000, function ($records) use (&$deleted, $bar) {
            foreach ($records as $record) {
                $record->delete();
                $deleted++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->info("Successfully deleted {$deleted} records.");
        
        return 0;
    }
}
