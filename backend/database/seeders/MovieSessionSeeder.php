<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovieSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payments')->delete();
        DB::table('tickets')->delete();
        DB::table('seats')->delete();
        DB::table('movieSessions')->delete();

        $movieIds = Movie::query()->pluck('id')->values()->all();

        if (empty($movieIds)) {
            return;
        }

        $hours = ['16:00', '18:00', '20:00'];
        $sessions = [];

        // Genera sesiones desde hoy hasta el final de la semana siguiente.
        $today = Carbon::today();
        $daysUntilNextSunday = (Carbon::SUNDAY - $today->dayOfWeek + 7) % 7;
        $lastSessionDate = $today->copy()->addDays($daysUntilNextSunday + 7);

        for ($sessionDate = $today->copy(); $sessionDate->lte($lastSessionDate); $sessionDate->addDay()) {
            $dayMovieIds = $movieIds;
            shuffle($dayMovieIds);

            foreach ($hours as $index => $hour) {
                $movieId = $dayMovieIds[$index % count($dayMovieIds)];

                $sessions[] = [
                    'movie_id' => $movieId,
                    'fecha' => $sessionDate->format('Y-m-d'),
                    'hora' => $hour,
                    'estado' => 'disponible',
                    'dia_espectador' => $sessionDate->isWednesday(),
                    'fila_vip_activa' => $hour !== '16:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insertar las sesiones
        DB::table('movieSessions')->insert($sessions);
        
        // Obtener todas las sesiones para crear asientos
        $insertedSessions = DB::table('movieSessions')->get();
        
        // Crear asientos para cada sesión
        foreach ($insertedSessions as $session) {
            $seats = [];
            $filas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
            
            foreach ($filas as $fila) {
                for ($numero = 1; $numero <= 10; $numero++) {
                    // Determinar si el asiento es VIP (fila K y L son VIP si fila_vip_activa es true)
                    $tipo = 'normal';
                    if ($session->fila_vip_activa && ($fila == 'F')) {
                        $tipo = 'vip';
                    }
                    
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
            
            // Insertar todos los asientos para esta sesión
            DB::table('seats')->insert($seats);
        }
    }
}
