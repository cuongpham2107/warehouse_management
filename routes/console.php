<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động dọn dẹp các bản backup cũ hơn 7 ngày lúc 01:00 hàng ngày
Schedule::command('backup:clean')->dailyAt('01:00');

// Tự động backup database lúc 01:30 hàng ngày (lưu trữ 7 ngày)
Schedule::command('backup:run --only-db')->dailyAt('01:30');
