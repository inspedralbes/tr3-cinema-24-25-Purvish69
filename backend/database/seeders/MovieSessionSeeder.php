<?php

namespace Database\Seeders;

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
        // Crear 4 sesiones diferentes con diferentes días, horas y películas
        $sessions = [
            [
                'movie_id' => 1, // Avengers: Endgame
                'fecha' => Carbon::now()->addDays(1)->format('Y-m-d'), // Mañana
                'hora' => '16:00',
                'estado' => 'disponible',
                'dia_espectador' => true,
                'fila_vip_activa' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 2, // The Dark Knight
                'fecha' => Carbon::now()->addDays(2)->format('Y-m-d'), // Pasado mañana
                'hora' => '18:00',
                'estado' => 'disponible',
                'dia_espectador' => false,
                'fila_vip_activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 3, // Interstellar
                'fecha' => Carbon::now()->addDays(3)->format('Y-m-d'), // En 3 días
                'hora' => '20:00',
                'estado' => 'disponible',
                'dia_espectador' => false,
                'fila_vip_activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 4, // Spider-Man: No Way Home
                'fecha' => Carbon::now()->addDays(4)->format('Y-m-d'), // En 4 días
                'hora' => '16:00',
                'estado' => 'disponible',
                'dia_espectador' => true,
                'fila_vip_activa' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('movieSessions')->insert($sessions);
    }
}
