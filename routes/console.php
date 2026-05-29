<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Invoice;

Schedule::call(function () {
    Invoice::where('created_at', '<', now()->subDays(10))->delete();
})->dailyAt('00:00');
