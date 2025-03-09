<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\MovieSession;
use Illuminate\Support\Facades\Validator;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    // Muestra todos los tickets 
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'movieSession.movie', 'seat']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('movieSession_id')) {
            $query->where('movieSession_id', $request->movieSession_id);
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
            'precio' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el ticket'
            ], 422);
        }

        // Verificar que la butaca pertenezca a la sesión y esté libre
        $seat = Seat::findOrFail($request->seat_id);
        if ($seat->session_id != $request->movieSession_id) {
            return response()->json([
                'error' => 'La butaca seleccionada no pertenece a la sesión indicada'
            ], 422);
        }
        if ($seat->estado !== 'libre') {
            return response()->json([
                'error' => 'La butaca seleccionada ya está ocupada'
            ], 422);
        }

        // Limitar a 10 tickets por usuario para la misma sesión
        $countTickets = Ticket::where('user_id', $request->user_id)
            ->where('movieSession_id', $request->movieSession_id)
            ->count();
        if ($countTickets >= 10) {
            return response()->json([
                'error' => 'El usuario ya ha comprado 10 entradas para esta película'
            ], 422);
        }

        // Evitar que el usuario tenga tickets para otra sesión futura
        $futureSessions = MovieSession::where('fecha', '>=', date('Y-m-d'))
            ->where('id', '!=', $request->movieSession_id)
            ->pluck('id');
        $ticketFuturo = Ticket::where('user_id', $request->user_id)
            ->whereIn('movieSession_id', $futureSessions)
            ->first();
        if ($ticketFuturo) {
            return response()->json([
                'error' => 'El usuario ya tiene tickets para otra sesión futura'
            ], 422);
        }

        // Crear ticket con un código único de confirmación
        $data = $request->all();
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
            'precio' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Si se cambia la butaca, se libera la anterior y se valida la nueva
        if ($request->has('seat_id') && $request->seat_id != $ticket->seat_id) {
            $oldSeat = Seat::findOrFail($ticket->seat_id);
            $oldSeat->update(['estado' => 'libre']);

            $newSeat = Seat::findOrFail($request->seat_id);
            // Usamos la sesión nueva si se envía o la ya asignada al ticket
            $sessionId = $request->movieSession_id ?? $ticket->movieSession_id;
            if ($newSeat->session_id != $sessionId) {
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

        $ticket->update($request->all());
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
}
