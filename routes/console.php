<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalan setiap menit untuk ngecek secara realtime
Schedule::command('tasks:check-overdue')->everyMinute();
