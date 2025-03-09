<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieSession;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MovieSessionsController extends Controller
{
    // Lista todas las sesiones de películas con la información de la película asociada
    public function index()
    {
        $sessions = MovieSession::with('movie')->get();
        return response()->json(['sessions' => $sessions]);
    }

    // Crea una nueva sesión de pelicula
    public function store(Request $request)
    {
        // Solo los administradores pueden crear sesiones
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        // Validar los datos de entrada
        $data = $request->all();
        $rules = [
            'movie_id'        => 'required|exists:movies,id',
            'fecha'           => 'required|date|after_or_equal:today',
            'hora'            => 'required|in:16:00,18:00,20:00',
            'dia_espectador'  => 'boolean',
            'fila_vip_activa' => 'boolean',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar que no exista ya una sesión con la misma fecha y hora
        $exists = MovieSession::where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->first();
        if ($exists) {
            return response()->json([
                'message' => 'Ya existe una sesión programada para esta fecha y hora'
            ], 422);
        }

        // Crear la sesión
        $session = MovieSession::create([
            'movie_id'        => $data['movie_id'],
            'fecha'           => $data['fecha'],
            'hora'            => $data['hora'],
            'estado'          => 'disponible',
            'dia_espectador'  => $data['dia_espectador'] ?? false,
            'fila_vip_activa' => $data['fila_vip_activa'] ?? false,
        ]);

        // Crear las butacas para la sesión
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        foreach ($rows as $row) {
            // Si la fila es F y se activó la opción VIP, se marca como 'vip'
            $seatType = ($row === 'F' && $session->fila_vip_activa) ? 'vip' : 'normal';
            for ($num = 1; $num <= 10; $num++) {
                Seat::create([
                    'movieSession_id' => $session->id,
                    'fila'            => $row,
                    'numero'          => $num,
                    'tipo'            => $seatType,
                    'estado'          => 'libre',
                ]);
            }
        }

        return response()->json([
            'session' => $session->load('movie'),
            'message' => 'Sesión de película creada con éxito'
        ], 201);
    }

    // Muestra los detalles de una sesión por su ID
    public function show($id)
    {
        $session = MovieSession::with(['movie', 'seats'])->findOrFail($id);
        return response()->json(['session' => $session]);
    }

    // Actualiza una sesión existente
    public function update(Request $request, $id)
    {
        // Solo los administradores pueden actualizar
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $session = MovieSession::findOrFail($id);

        // No se puede modificar una sesión ya finalizada
        if ($session->estado === 'finalizada') {
            return response()->json([
                'message' => 'No se puede modificar una sesión finalizada'
            ], 422);
        }

        // Validar datos a actualizar
        $rules = [
            'estado'          => 'sometimes|in:disponible,cancelada,finalizada',
            'dia_espectador'  => 'sometimes|boolean',
            'fila_vip_activa' => 'sometimes|boolean',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualizar los campos de la sesión
        $session->update($request->only(['estado', 'dia_espectador', 'fila_vip_activa']));

        // Si se modificó la opción de fila VIP, se actualizan las butacas de la fila F que estén libres
        if ($request->has('fila_vip_activa')) {
            Seat::where('movieSession_id', $session->id)
                ->where('fila', 'F')
                ->where('estado', 'libre')
                ->update([
                    'tipo' => $request->fila_vip_activa ? 'vip' : 'normal'
                ]);
        }

        return response()->json([
            'session' => $session->fresh()->load('movie'),
            'message' => 'Sesión actualizada con éxito'
        ]);
    }

    // Elimina una sesión siempre que no tenga entradas vendidas
    public function destroy($id)
    {
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $session = MovieSession::findOrFail($id);
        if ($session->tickets()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar una sesión con entradas vendidas'
            ], 422);
        }

        $session->delete();
        return response()->json(['message' => 'Sesión eliminada con éxito']);
    }

    // Obtiene las butacas de una sesión, ordenadas por fila y número
    public function getSeats($id)
    {
        $session = MovieSession::findOrFail($id);
        $seats = Seat::where('movieSession_id', $id)
            ->orderBy('fila')
            ->orderBy('numero')
            ->get();

        return response()->json([
            'movie_session' => $session->load('movie'),
            'seats'         => $seats,
        ]);
    }
}
