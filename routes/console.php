<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
});

// Hapus folder log harian (storage/logs/<yyyy-mm-dd>) yang lebih tua dari --days
// (default LOG_KEEP_DAYS / 60). Biasanya tidak perlu dijalankan: log pertama tiap hari
// sudah menjalankannya otomatis.
Artisan::command('logs:prune {--days= : jumlah hari yang disimpan}', function () {
    $days = $this->option('days') !== null ? (int) $this->option('days') : null;
    $deleted = App\Logging\ControllerLog::prune($days);
    $this->info($deleted ? 'Dihapus: ' . implode(', ', $deleted) : 'Tidak ada log lama yang dihapus.');
})->purpose('Hapus log harian yang lebih tua dari N hari');
