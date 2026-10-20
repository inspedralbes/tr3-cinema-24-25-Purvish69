<?php

use App\Services\MovieSessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:generate {--days= : Número de días a generar (por defecto hasta fin de la próxima semana)} {--fresh : Borrar sesiones existentes antes de generar}', function (MovieSessionService $service) {
    /** @var ClosureCommand $this */
    $days = $this->option('days') !== null ? (int) $this->option('days') : null;
    $fresh = (bool) $this->option('fresh');

    $this->info('Generando sesiones de película con fechas actuales...');

    $created = $service->generateSessions($days, $fresh);

    $today = Carbon::today(config('app.timezone', 'Europe/Madrid'))->format('Y-m-d');
    $this->info("Se han generado {$created} sesiones exitosamente a partir de {$today}.");
})->purpose('Generar sesiones de películas para días actuales');
