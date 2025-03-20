<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\MovieSession;
use Illuminate\Support\Facades\Validator;
use App\Models\Seat;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

class TicketController extends Controller
{

    // Función para obtener los tickets de una sesión
    public function getSessionTickets($sessionId)
    {
        try {
            $tickets = Ticket::where('movieSession_id', $sessionId)->with(['seat', 'user'])->get();
            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching session tickets: ' . $e->getMessage()], 500);
        }
    }

    // Función para calcular el precio basado en el tipo de butaca y el campo dia_espectador
    private function calcularPrecio($sessionId, $seatId)
    {
        $session = MovieSession::findOrFail($sessionId);
        $seat = Seat::findOrFail($seatId);

        // Precios según el campo dia_espectador de la sesión
        if ($session->dia_espectador) {
            // Precios reducidos para día del espectador
            $precioNormal = 4; // Precio butaca normal
            $precioVIP = 6;    // Precio butaca VIP
        } else {
            // Precios normales
            $precioNormal = 6; // Precio butaca normal
            $precioVIP = 8;    // Precio butaca VIP
        }

        // Devolvemos el precio según el tipo de butaca
        return ($seat->tipo === 'vip') ? $precioVIP : $precioNormal;
    }

    // Muestra todos los tickets 
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'movieSession.movie', 'seat', 'payment']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('movieSession_id')) {
            $query->where('movieSession_id', $request->movieSession_id);
        }
        if ($request->has('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        return response()->json($query->get());
    }
    // Crea un nuevo ticket
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'movieSession_id' => 'required|exists:movieSessions,id',
            'seat_id' => 'required|exists:seats,id',
            'payment_id' => 'required|exists:payments,id',
            'precio' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el ticket',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que la butaca pertenezca a la sesión y esté libre
        $seat = Seat::findOrFail($request->seat_id);
        if ($seat->movieSession_id != $request->movieSession_id) {
            return response()->json([
                'error' => 'La butaca seleccionada no pertenece a la sesión indicada'
            ], 422);
        }
        if ($seat->estado !== 'libre') {
            return response()->json([
                'error' => 'La butaca seleccionada ya está ocupada'
            ], 422);
        }

        // Verificar que el pago pertenezca al usuario
        $payment = Payment::findOrFail($request->payment_id);
        if ($payment->user_id != $request->user_id) {
            return response()->json([
                'error' => 'El pago no pertenece al usuario indicado'
            ], 422);
        }

        // Calcular el precio si no fue proporcionado
        $data = $request->all();
        if (!isset($data['precio'])) {
            $data['precio'] = $this->calcularPrecio($request->movieSession_id, $request->seat_id);
        }

        // Crear ticket con un código único de confirmación
        $data['codigo_confirmacion'] = Str::uuid();
        $ticket = Ticket::create($data);

        // Marcar la butaca como ocupada
        $seat->update(['estado' => 'ocupada']);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket creado exitosamente',
            'data' => $ticket
        ], 201);
    }


    // Muestra un ticket específico
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'movieSession.movie', 'seat']);
        return response()->json($ticket);
    }

    // Actualiza un ticket existente
    public function update(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'movieSession_id' => 'sometimes|required|exists:movieSessions,id',
            'seat_id' => 'sometimes|required|exists:seats,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Si se cambia la butaca o la sesión, recalculamos el precio
        if ($request->has('seat_id') || $request->has('movieSession_id')) {
            $sessionId = $request->movieSession_id ?? $ticket->movieSession_id;
            $seatId = $request->seat_id ?? $ticket->seat_id;

            // Si se cambia la butaca, se libera la anterior y se valida la nueva
            if ($request->has('seat_id') && $request->seat_id != $ticket->seat_id) {
                $oldSeat = Seat::findOrFail($ticket->seat_id);
                $oldSeat->update(['estado' => 'libre']);

                $newSeat = Seat::findOrFail($request->seat_id);
                // Usamos la sesión nueva si se envía o la ya asignada al ticket
                if ($newSeat->movieSession_id != $sessionId) {
                    return response()->json([
                        'error' => 'La butaca seleccionada no corresponde a la sesión'
                    ], 422);
                }
                if ($newSeat->estado !== 'libre') {
                    return response()->json([
                        'error' => 'La butaca seleccionada ya está ocupada'
                    ], 422);
                }
                $newSeat->update(['estado' => 'ocupada']);
            }

            // Recalcular precio
            $data['precio'] = $this->calcularPrecio($sessionId, $seatId);
        }

        $ticket->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket actualizado exitosamente',
            'data' => $ticket
        ]);
    }

    // Elimina un ticket y libera la butaca asociada
    public function destroy(Ticket $ticket)
    {
        $seat = Seat::findOrFail($ticket->seat_id);
        $seat->update(['estado' => 'libre']);

        $ticket->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket eliminado exitosamente'
        ], 200);
    }

    // Nueva función para obtener los precios actuales según una sesión
    public function getPreciosSesion($sessionId)
    {
        $session = MovieSession::findOrFail($sessionId);

        // Precios según valor de dia_espectador
        if ($session->dia_espectador) {
            $precios = [
                'normal' => 4,
                'vip' => 6
            ];
        } else {
            $precios = [
                'normal' => 6,
                'vip' => 8
            ];
        }

        return response()->json([
            'precios' => $precios,
            'dia_espectador' => $session->dia_espectador,
            'fecha' => $session->fecha,
            'pelicula' => $session->movie ? $session->movie->titulo : null
        ]);
    }
}
