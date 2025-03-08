<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use App\Models\Seat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class MovieSessionsController extends Controller
{
    // mostrar todas las sesiones de peliculas
    public function index()
    {
        $movieSessions = MovieSession::with('movie')->get();
        return response()->json($movieSessions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'fecha'    => 'required|date',
            'hora' => 'required|in:16:00,18:00,20:00',
            'estado' => 'sometimes|in:disponible,cancelada,finalizada',
            'dia_espectador' => 'sometimes|boolean',
            'fila_vip_activa' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear la sesión',
                'errors'  => $validator->errors()
            ], 422);
        }

        // verifiacion que no exista una sesion con la misma fecha y hora
        $existingSession = MovieSession::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->first();

        if ($existingSession) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ya existe una sesión para la misma fecha y hora',
            ], 422);
        }

        $movieSession = MovieSession::create($request->all());

        // Crear asientos automaticamente para esta sesión
        $this->createSeats($movieSession);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sesi&oacute;n creada exitosamente',
            'data'    => $movieSession
        ], 201);
    }

    private function createSeats($movieSession)
    {
        $filas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        foreach ($filas as $fila) {
            for ($numero = 1; $numero <= 10; $numero++) {
                Seat::create([
                    'movieSession_id' => $movieSession->id,
                    'fila' => $fila,
                    'numero' => $numero,
                    'tipo' => ($fila == 'F') ? 'vip' : 'normal', // fila F es para vip
                    'estado' => 'libre',
                ]);
            }
        }
    }

    // mostrar la sesion de pelicula 
    public function show($id)
    {
        // Obtener la instancia de MovieSession
        $movieSession = MovieSession::with('movie', 'seats')->findOrFail($id);
        return response()->json($movieSession);
    }

    // actualizar la sesion de pelicula
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'sometimes|required|exists:movies,id',
            'fecha' => 'sometimes|required|date',
            'hora' => 'sometimes|required|in:16:00,18:00,20:00',
            'estado' => 'sometimes|in:disponible,cancelada,finalizada',
            'dia_espectador' => 'sometimes|boolean',
            'fila_vip_activa' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar la sesión',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Obtener la instancia de MovieSession
        $movieSession = MovieSession::findOrFail($id);

        // si cambia la fecha o hora verificar que no exista otra sesion
        if (($request->has('fecha') || $request->has('hora')) && ($request->fecha != $movieSession->fecha || $request->hora != $movieSession->hora)) {

            $existingSession = MovieSession::where('fecha', $request->fecha ?? $movieSession->fecha)
                ->where('hora', $request->hora ?? $movieSession->hora)
                ->where('id', '!=', $movieSession->id)
                ->first();

            if ($existingSession) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Ya existe una sesión para la misma fecha y hora',
                ], 422);
            }
        }

        $movieSession->update($request->all());

        return response()->json($movieSession);
    }

    public function destroy($id)
    {
        // Obtener la instancia de MovieSession
        $movieSession = MovieSession::findOrFail($id);

        // Comprobar si hay entradas vendidas
        if ($movieSession->tickets()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar la sesion porque hay entradas vendidas'
            ], 422);
        }

        $movieSession->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'sesion de pelicula eliminada exitosamente'
        ], 200);
    }
}