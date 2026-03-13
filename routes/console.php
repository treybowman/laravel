<?php

use App\Jobs\EventExpireJob;
use App\Jobs\ListingExpireJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new EventExpireJob)->hourly();
Schedule::job(new ListingExpireJob)->hourly();
Schedule::command('events:sync')->dailyAt('02:00');
