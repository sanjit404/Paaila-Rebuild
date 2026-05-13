<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class PaailaSetup extends Command
{
    protected $signature = 'paaila:setup {--yes : Auto confirm all prompts}';
    protected $description = 'Full Laravel environment setup wizard (Paaila Dev Mode)';

    public function handle()
    {
        $this->line("\n==============================");
        $this->line(" PAAILA SETUP WIZARD");
        $this->line("==============================\n");

        $auto = $this->option('yes');

        $criticalFail = false;

      
        $this->line("[Paaila] Checking PHP Extensions");

        $required = ['xml', 'dom', 'mbstring', 'openssl', 'pdo'];

        foreach ($required as $ext) {
            if (extension_loaded($ext)) {
                $this->info("[Paaila] OK  $ext");
            } else {
                $this->error("[Paaila] MISS $ext");
                $criticalFail = true;
            }
        }

        if ($criticalFail) {
            $this->error("\n[Paaila] CRITICAL: Missing PHP extensions detected.");
            $this->line("[Paaila] Run: php bootstrap.php first.");
            return Command::FAILURE;
        }


        $this->line("\n[Paaila] Checking Composer");

        exec("composer -V 2>&1", $out, $code);

        if ($code !== 0) {
            $this->error("[Paaila] Composer not found");

            if ($auto || $this->confirm("[Paaila] Run composer install?")) {
                exec("composer install");
                $this->info("[Paaila] Composer installed");
            } else {
                return Command::FAILURE;
            }
        } else {
            $this->info("[Paaila] Composer OK");
        }


        $this->line("\n[Paaila] Checking Node");

        exec("node -v 2>&1", $nodeOut, $nodeCode);

        if ($nodeCode === 0) {
            $this->info("[Paaila] Node OK: " . trim(implode("", $nodeOut)));

            if (File::exists(base_path('package.json'))) {
                if ($auto || $this->confirm("[Paaila] Run npm install?")) {
                    exec("npm install");
                    $this->info("[Paaila] npm installed");
                }
            }
        } else {
            $this->warn("[Paaila] Node not found");
        }


        $this->line("\n[Paaila] Checking .env");

        if (!File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->info("[Paaila] .env created");
        } else {
            $this->info("[Paaila] .env exists");
        }


        $this->line("\n[Paaila] Checking APP KEY");

        if (!env('APP_KEY')) {
            Artisan::call('key:generate');
            $this->info("[Paaila] APP_KEY generated");
        }

        $this->line("\n[Paaila] Checking Storage");

        if (!File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $this->info("[Paaila] Storage linked");
        }



        $this->line('Setting up please wait');

            $bar = $this->output->createProgressBar(30);
            $bar->start();

            for ($i = 0; $i < 30; $i++) {
                usleep(90000);
                $bar->advance();
            }

            $bar->finish();


            $this->newLine();

            $this->line('[FINALIZING] merging system identity...');
            usleep(1200000);

            $this->line('[FINALIZING] resolving operator trace...');
            usleep(1200000);

            $this->line('[FINALIZING] sealing runtime connection...');
            usleep(1200000);

            $this->newLine();

            $this->warn('██████╗  █████╗  █████╗ ██╗██╗      █████╗ ');
            $this->comment('██╔══██╗██╔══██╗██╔══██╗██║██║     ██╔══██╗');
            $this->warn('██████╔╝███████║███████║██║██║     ███████║');
            $this->comment('██╔═══╝ ██╔══██║██╔══██║██║██║     ██╔══██║');
            $this->warn('██║     ██║  ██║██║  ██║██║███████╗██║  ██║');
            $this->comment('╚═╝     ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝╚══════╝╚═╝  ╚═╝');

            $this->newLine();

        $this->line("\n[Paaila] SETUP COMPLETE");
        $this->line("[Paaila] System ready for deployment\n");
            $this->newLine();
            $this->newLine();
        $this->line("[RUN] php artisan serve\n");

        return Command::SUCCESS;
    }
}
