<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;

class ExampleJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('ExampleJob executed successfully.');
        
        // Try different logging methods
        \Log::channel('single')->info('ExampleJob - Single channel log');
        logger('ExampleJob - Logger helper');
        
    }
}
