<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hapus tracking lama (lebih dari 30 hari) setiap malam
Schedule::command('model:prune', ['--model' => ['App\Models\DeliveryTracking']])->daily();
