<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    // Obtener todos los asientos con opción de filtrar por movieSession_id
    public function index()
    {
        $query = Seat::query();

        if (request()->has('movieSession_id')) {
            $query->where('movieSession_id', request('movieSession_id'));
        }

        $seats = $query->get();
        return response()->json($seats);
    }

    // Crear un nuevo asiento
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movieSession_id' => 'required|exists:movieSessions,id',
            'fila' => 'required|string|size:1|in:A,B,C,D,E,F,G,H,I,J,K,L',
            'numero' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:normal,vip',
            'estado' => 'required|in:libre,ocupada',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar que no exista ya esta butaca para esta sesión
        $existingSeat = Seat::where('movieSession_id', $request->movieSession_id)
            ->where('fila', $request->fila)
            ->where('numero', $request->numero)
            ->first();

        if ($existingSeat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ya existe una butaca para esta sesión',
            ], 422);
        }

        $seat = Seat::create($request->all());
        return response()->json($seat, 201);
    }

    // Mostrar un asiento específico
    public function show($id)
    {
        $seat = Seat::findOrFail($id);
        return response()->json($seat);
    }

    // Actualizar un asiento
    public function update(Request $request, $id)
    {
        $seat = Seat::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'movieSession_id' => 'sometimes|required|exists:movieSessions,id',
            'fila' => 'sometimes|required|string|size:1|in:A,B,C,D,E,F,G,H,I,J,K,L',
            'numero' => 'sometimes|required|integer|min:1|max:10',
            'tipo' => 'sometimes|required|in:normal,vip',
            'estado' => 'sometimes|required|in:libre,ocupada',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seat->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Butaca actualizada exitosamente',
            'data' => $seat
        ]);
    }

    // Eliminar un asiento
    public function destroy($id)
    {
        $seat = Seat::findOrFail($id);

        // Verificar si tiene un ticket asociado
        if ($seat->tickets()->exists()) {
            return response()->json([
                'error' => 'No se puede eliminar la butaca porque tiene un ticket asociado'
            ], 422);
        }

        $seat->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Butaca eliminada exitosamente'
        ]);
    }
}
