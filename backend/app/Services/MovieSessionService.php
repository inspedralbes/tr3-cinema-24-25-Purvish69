<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieSession;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovieSessionService
{
    /**
     * Genera sesiones desde hoy para el número de días indicado.
     *
     * @param int|null $days Número de días (si es null, hasta el final de la siguiente semana)
     * @param bool $fresh Si es true, vacía sesiones y asientos previos
     * @return int Número de sesiones creadas
     */
    public function generateSessions(?int $days = null, bool $fresh = false): int
    {
        $movieIds = Movie::query()->pluck('id')->values()->all();

        if (empty($movieIds)) {
            return 0;
        }

        if ($fresh) {
            DB::table('payments')->delete();
            DB::table('tickets')->delete();
            DB::table('seats')->delete();
            DB::table('movieSessions')->delete();
        }

        $hours = ['16:00', '18:00', '20:00'];
        $today = Carbon::today(config('app.timezone', 'Europe/Madrid'));

        if ($days !== null && $days > 0) {
            $lastSessionDate = $today->copy()->addDays($days - 1);
        } else {
            $daysUntilNextSunday = (Carbon::SUNDAY - $today->dayOfWeek + 7) % 7;
            $lastSessionDate = $today->copy()->addDays($daysUntilNextSunday + 7);
        }

        $createdCount = 0;
        $movieSessionCounts = MovieSession::whereDate('fecha', '>=', $today->toDateString())
            ->select('movie_id', DB::raw('COUNT(*) as sessions_count'))
            ->groupBy('movie_id')
            ->pluck('sessions_count', 'movie_id')
            ->all();

        for ($sessionDate = $today->copy(); $sessionDate->lte($lastSessionDate); $sessionDate->addDay()) {
            $fechaStr = $sessionDate->format('Y-m-d');

            foreach ($hours as $hour) {
                // Si ya existe una sesión en esta fecha y hora, no duplicar
                if (!$fresh && MovieSession::where('fecha', $fechaStr)->where('hora', $hour)->exists()) {
                    continue;
                }

                // No programar una película más de dos veces en la cartelera futura.
                $availableMovieIds = array_values(array_filter(
                    $movieIds,
                    fn ($movieId) => ($movieSessionCounts[$movieId] ?? 0) < 2
                ));

                if (empty($availableMovieIds)) {
                    continue;
                }

                $movieId = $availableMovieIds[array_rand($availableMovieIds)];

                $session = MovieSession::create([
                    'movie_id' => $movieId,
                    'fecha' => $fechaStr,
                    'hora' => $hour,
                    'estado' => 'disponible',
                    'dia_espectador' => $sessionDate->isWednesday(),
                    'fila_vip_activa' => $hour !== '16:00',
                ]);

                // Generar los 120 asientos (filas A-L, 10 asientos por fila)
                $seats = [];
                $filas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                foreach ($filas as $fila) {
                    for ($numero = 1; $numero <= 10; $numero++) {
                        $tipo = ($session->fila_vip_activa && $fila === 'F') ? 'vip' : 'normal';
                        $seats[] = [
                            'movieSession_id' => $session->id,
                            'fila' => $fila,
                            'numero' => $numero,
                            'tipo' => $tipo,
                            'estado' => 'libre',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                DB::table('seats')->insert($seats);
                $movieSessionCounts[$movieId] = ($movieSessionCounts[$movieId] ?? 0) + 1;
                $createdCount++;
            }
        }

        return $createdCount;
    }
}
