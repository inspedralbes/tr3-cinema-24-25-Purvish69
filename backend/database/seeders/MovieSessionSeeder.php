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
