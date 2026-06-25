<?php

use Illuminate\Support\Facades\Schedule;

// Generate tagihan bulanan setiap tanggal 1
Schedule::command('membership:generate-bills')
    ->monthlyOn(1, '00:00')
    ->withoutOverlapping();

// Cek tunggakan dan pembekuan setiap hari
Schedule::command('membership:check-overdue')
    ->dailyAt('06:00')
    ->withoutOverlapping();
