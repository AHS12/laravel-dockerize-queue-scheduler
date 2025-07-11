<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:example-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A scheduled command that logs an info message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('ExampleCommand executed successfully from schedular.');
        $this->info('ExampleCommand executed successfully.');
        return 0;
    }
}
