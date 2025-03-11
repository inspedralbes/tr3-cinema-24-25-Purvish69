<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return response()->json(['movies' => $movies]);
    }

    public function store(Request $request)
    {
        // Verificar si el usuario es administrador
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'director' => 'nullable|string|max:255',
            'actores' => 'required|array',
            'duracion' => 'required|integer|min:1',
            'clasificacion' => 'required|string|max:10',
            'genero' => 'required|string|max:100',
            'lenguaje' => 'required|string|max:100',
            'imagen' => 'nullable|string|max:2048', // Acepta tanto URL como archivo
            'trailer' => 'nullable|string|max:255',
            'omdb_id' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('imagen'); // Excluimos imagen inicialmente

        // Si se sube un archivo, lo guardamos en el servidor
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('public/images');
            $data['imagen'] = Storage::url($path);
        } elseif ($request->filled('imagen')) {
            // Si el campo 'imagen' tiene un valor, lo guardamos (puede ser una URL)
            $data['imagen'] = $request->imagen;
        } else {
            // Si no hay imagen, guardamos NULL
            $data['imagen'] = null;
        }

        $movie = Movie::create($data);

        return response()->json(['movie' => $movie, 'message' => 'Película creada con éxito'], 201);
    }



    public function show($id)
    {
        $movie = Movie::with('sessions')->findOrFail($id);
        return response()->json(['movie' => $movie]);
    }

    public function update(Request $request, $id)
    {
        // Verificar que el usuario autenticado sea administrador
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $movie = Movie::findOrFail($id);

        // Validación de datos
        $data = $request->validate([
            'titulo'         => 'sometimes|string|max:255',
            'descripcion'    => 'sometimes|string',
            'director'       => 'nullable|string|max:255',
            'actores'        => 'sometimes|array',
            'duracion'       => 'sometimes|integer|min:1',
            'clasificacion'  => 'sometimes|string|max:10',
            'genero'         => 'sometimes|string|max:100',
            'lenguaje'       => 'sometimes|string|max:100',
            'imagen'         => 'nullable|image|max:2048',
            'trailer'        => 'sometimes|string|max:255',
            'omdb_id'        => 'sometimes|string|max:20',
        ]);

        // Si hay nueva imagen, eliminar la anterior y guardar la nueva
        if ($request->hasFile('imagen')) {
            if ($movie->imagen && Storage::exists(str_replace('/storage', 'public', $movie->imagen))) {
                Storage::delete(str_replace('/storage', 'public', $movie->imagen));
            }
            $imagenPath = $request->file('imagen')->store('public/movies');
            $data['imagen'] = Storage::url($imagenPath);
        }

        $movie->update($data);

        return response()->json(['movie' => $movie, 'message' => 'Película actualizada con éxito']);
    }

    public function destroy($id)
    {
        // Verificar si el usuario es administrador
        if (Auth::user()->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $movie = Movie::findOrFail($id);

        // Eliminar la imagen si existe
        if ($movie->imagen && Storage::exists(str_replace('/storage', 'public', $movie->imagen))) {
            Storage::delete(str_replace('/storage', 'public', $movie->imagen));
        }

        $movie->delete();
        return response()->json(['message' => 'Película eliminada con éxito']);
    }
}
