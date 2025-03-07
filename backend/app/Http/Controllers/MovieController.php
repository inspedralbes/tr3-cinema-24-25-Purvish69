<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return response()->json([
            'status'  => 'success',
            'message' => 'Películas obtenidas exitosamente',
            'data'    => $movies
        ], 200);
    }

    // Crear una película
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'        => 'required|string|max:255',
            'descripcion'   => 'required|string',
            'director'      => 'nullable|string|max:255',
            'actores'       => 'nullable|array',
            'duracion'      => 'required|integer|min:1',
            'clasificacion' => 'required|string|max:10',
            'imagen'        => 'nullable|url',
            'trailer'       => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear la película',
                'errors'  => $validator->errors()
            ], 422);
        }

        $movie = Movie::create($request->all());
        return response()->json([
            'status'  => 'success',
            'message' => 'Película creada exitosamente',
            'data'    => $movie
        ], 201);
    }

    // Mostrar una película
    public function show(Movie $id)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Detalles de la película',
            'data'    => $id
        ], 200);
    }

    // Actualizar una película
    public function update(Request $request, Movie $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo'        => 'sometimes|required|string|max:255',
            'descripcion'   => 'sometimes|required|string',
            'director'      => 'nullable|string|max:255',
            'actores'       => 'nullable|array',
            'duracion'      => 'sometimes|required|integer|min:1',
            'clasificacion' => 'sometimes|required|string|max:10',
            'imagen'        => 'nullable|url',
            'trailer'       => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar la película',
                'errors'  => $validator->errors()
            ], 422);
        }

        $id->update($request->all());
        return response()->json([
            'status'  => 'success',
            'message' => 'Película actualizada exitosamente',
            'data'    => $id
        ], 200);
    }

    // Eliminar una película
    public function destroy(Movie $id)
    {
        $id->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Película eliminada exitosamente'
        ], 200);
    }
}
