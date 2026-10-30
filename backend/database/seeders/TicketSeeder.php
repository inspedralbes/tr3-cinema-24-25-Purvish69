<?php

namespace Database\Seeders;

use App\Models\MovieSession;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Solo sembrar si no hay tickets
        if (Ticket::count() > 0) {
            return;
        }

        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        // Obtener las primeras 3 sesiones disponibles
        $sessions = MovieSession::with('movie')->where('estado', 'disponible')->take(3)->get();
        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            foreach ($sessions->take(2) as $session) {
                // Obtener 2 asientos libres para esta sesión
                $seats = Seat::where('movieSession_id', $session->id)
                    ->where('estado', 'libre')
                    ->take(2)
                    ->get();

                if ($seats->isEmpty()) {
                    continue;
                }

                $totalAmount = 0;
                foreach ($seats as $seat) {
                    $precio = ($seat->tipo === 'vip') ? 8.00 : 6.00;
                    if ($session->dia_espectador) {
                        $precio = ($seat->tipo === 'vip') ? 6.00 : 4.00;
                    }
                    $totalAmount += $precio;
                }

                // Crear pago completado
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'amount' => $totalAmount,
                    'payment_method' => 'card',
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                ]);

                foreach ($seats as $seat) {
                    $precio = ($seat->tipo === 'vip') ? 8.00 : 6.00;
                    if ($session->dia_espectador) {
                        $precio = ($seat->tipo === 'vip') ? 6.00 : 4.00;
                    }

                    $uuid = (string) Str::uuid();

                    Ticket::create([
                        'user_id' => $user->id,
                        'movieSession_id' => $session->id,
                        'seat_id' => $seat->id,
                        'payment_id' => $payment->id,
                        'precio' => $precio,
                        'codigo_confirmacion' => $uuid,
                        'qr_code' => null,
                    ]);

                    $seat->update(['estado' => 'ocupada']);
                }
            }
        }
    }
}
