<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MovieSession;
use App\Models\Seat;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketMail;

class TicketController extends Controller
{
    // Función para obtener los tickets de una sesión
    public function getSessionTickets($sessionId)
    {
        try {
            $tickets = Ticket::where('movieSession_id', $sessionId)
                ->with(['seat', 'user'])
                ->get();
            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tickets de la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    // Función para calcular el precio según el tipo de butaca y el campo dia_espectador de la sesión
    private function calcularPrecio($sessionId, $seatId)
    {
        $session = MovieSession::findOrFail($sessionId);
        $seat = Seat::findOrFail($seatId);

        // Precios según si es día espectador o no
        if ($session->dia_espectador) {
            $precioNormal = 4; // Precio butaca normal en día espectador
            $precioVIP    = 6; // Precio butaca VIP en día espectador
        } else {
            $precioNormal = 6; // Precio normal
            $precioVIP    = 8; // Precio VIP
        }

        return ($seat->tipo === 'vip') ? $precioVIP : $precioNormal;
    }

    // Muestra todos los tickets (con relaciones)
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

    // Crea un nuevo ticket y envía el correo con PDF adjunto
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required|exists:users,id',
            'movieSession_id' => 'required|exists:movieSessions,id',
            'seat_id'         => 'required|exists:seats,id',
            'payment_id'      => 'required|exists:payments,id',
            'precio'          => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear el ticket',
                'errors'  => $validator->errors()
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

        // Calcular el precio si no se envía
        $data = $request->all();
        if (!isset($data['precio'])) {
            $data['precio'] = $this->calcularPrecio($request->movieSession_id, $request->seat_id);
        }

        $data['codigo_confirmacion'] = Str::uuid();
        $ticket = Ticket::create($data);
        $seat->update(['estado' => 'ocupada']);

        // Enviar el correo con el PDF adjunto para la sesión actual
        $this->sendTicketsByEmail($ticket->user_id, $ticket->movieSession_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket creado y correo enviado correctamente',
            'data'    => $ticket
        ], 201);
    }

    // Muestra un ticket específico
    public function show($id)
    {
        $ticket = Ticket::with(['user', 'movieSession.movie', 'seat', 'payment'])->find($id);
        
        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket no encontrado'
            ], 404);
        }
        
        return response()->json($ticket);
    }

    // Actualiza un ticket existente
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'user_id'         => 'sometimes|required|exists:users,id',
            'movieSession_id' => 'sometimes|required|exists:movieSessions,id',
            'seat_id'         => 'sometimes|required|exists:seats,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Si se cambia la butaca o la sesión, se recalcula el precio
        if ($request->has('seat_id') || $request->has('movieSession_id')) {
            $sessionId = $request->movieSession_id ?? $ticket->movieSession_id;
            $seatId    = $request->seat_id ?? $ticket->seat_id;

            // Si se cambia la butaca, se libera la anterior y se valida la nueva
            if ($request->has('seat_id') && $request->seat_id != $ticket->seat_id) {
                $oldSeat = Seat::findOrFail($ticket->seat_id);
                $oldSeat->update(['estado' => 'libre']);

                $newSeat = Seat::findOrFail($request->seat_id);
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
        
        // Cargar relaciones después de actualizar
        $ticket->load(['user', 'movieSession.movie', 'seat', 'payment']);
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket actualizado exitosamente',
            'data'    => $ticket
        ]);
    }

    // Elimina un ticket y libera la butaca asociada
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $seat = Seat::findOrFail($ticket->seat_id);
        $seat->update(['estado' => 'libre']);

        $ticket->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Ticket eliminado correctamente'
        ], 200);
    }

    // Función para obtener los precios actuales según la sesión
    public function getPreciosSesion($sessionId)
    {
        $session = MovieSession::findOrFail($sessionId);

        if ($session->dia_espectador) {
            $precios = [
                'normal' => 4,
                'vip'    => 6
            ];
        } else {
            $precios = [
                'normal' => 6,
                'vip'    => 8
            ];
        }

        return response()->json([
            'precios'        => $precios,
            'dia_espectador' => $session->dia_espectador,
            'fecha'          => $session->fecha,
            'pelicula'       => $session->movie ? $session->movie->titulo : null
        ]);
    }
    
    // Obtiene todos los tickets de un usuario con todas las relaciones
    public function getUserTicketsComplete($userId)
    {
        $tickets = Ticket::where('user_id', $userId)
            ->with(['movieSession.movie', 'seat', 'payment', 'user'])
            ->get();

        return response()->json($tickets);
    }

    // Muestra un ticket con el usuario asociado
    public function showWithUser($id)
    {
        $ticket = Ticket::with(['user'])->find($id);
        
        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket no encontrado'
            ], 404);
        }
        
        return response()->json($ticket);
    }
    
    public function sendTicketsByEmail($userId, $sessionId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Obtenemos solo el ticket más reciente (último comprado) para este usuario y sesión
            $ticket = Ticket::where('user_id', $userId)
                ->where('movieSession_id', $sessionId)
                ->with(['movieSession.movie', 'seat'])
                ->latest('created_at') 
                ->first();             

            if (!$ticket) {
                return response()->json([
                    'error' => 'No se encontraron tickets para este usuario en la sesión indicada'
                ], 404);
            }

           
            $session = $ticket->movieSession;
            $sessionInfo = [
                'fecha' => $session->fecha,
                'hora'  => $session->hora,
                'sala'  => $session->sala,
                'movie' => $session->movie
            ];

            $ticketsCollection = collect([$ticket]);

            // Enviar el correo utilizando el mailable con PDF adjunto
            Mail::to($user->email)->send(new TicketMail($user, $ticketsCollection, $sessionInfo));

            return response()->json([
                'message' => 'Correo enviado correctamente con el último ticket comprado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }
}
