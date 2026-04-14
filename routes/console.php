<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the queue worker to process due posts
// This runs every minute to check for posts ready to publish
Schedule::command('queue:work --queue=default --max-time=55 --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
