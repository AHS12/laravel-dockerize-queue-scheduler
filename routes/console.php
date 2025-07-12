<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the ExampleCommand to run every minute
Schedule::command('app:example-command')->everyMinute();

// Clear Telescope entries daily
Schedule::command('telescope:prune --hours=24')->daily();
