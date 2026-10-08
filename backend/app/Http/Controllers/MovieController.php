<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    // Listado de películas
    public function index(Request $request)
    {
        $movies = Movie::all();

        if ($request->wantsJson()) {
            return response()->json(['movies' => $movies]);
        }

        return view('movies.index', compact('movies'));
    }

    // Mostrar formulario para crear película (Blade)
    public function create()
    {
        return view('movies.create');
    }

    // Almacenar nueva película
    public function store(Request $request)
    {
        // Verificar si el usuario es administrador
        if (Auth::user()->rol !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $validator = Validator::make($request->all(), [
            'titulo'         => 'required|string|max:255',
            'descripcion'    => 'required|string',
            'calificacion'   => 'nullable|string|max:10', 
            'director'       => 'nullable|string|max:255',
            'actores'        => 'required|string',
            'duracion'       => 'required|integer|min:1',
            'clasificacion'  => 'required|string|max:10',
            'genero'         => 'required|string|max:100',
            'lenguaje'       => 'required|string|max:100',
            'imagen'         => 'nullable|string|max:2048',
            'poster'         => 'nullable|string|max:2048',
            'trailer'        => 'nullable|string|max:255',
            'omdb_id'        => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('imagen');
        $data['actores'] = array_map('trim', explode(',', $request->input('actores')));

        // Si se sube un archivo se guarda en el servidor, de lo contrario se toma la URL
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('public/images');
            $data['imagen'] = Storage::url($path);
        } elseif ($request->filled('imagen')) {
            $data['imagen'] = $request->imagen;
        } else {
            $data['imagen'] = null;
        }

        $movie = Movie::create($data);

        if ($request->wantsJson()) {
            return response()->json(['movie' => $movie, 'message' => 'Película creada con éxito'], 201);
        }
        return redirect()->route('movies.index')->with('success', 'Película creada con éxito');
    }

    // Mostrar detalle de la película
    public function show(Request $request, $id)
    {
        $movie = Movie::with('sessions')->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json(['movie' => $movie]);
        }
        return view('movies.show', compact('movie'));
    }

    // Mostrar formulario para editar película (Blade)
    public function edit(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        return view('movies.edit', compact('movie'));
    }

    // Actualizar película existente
    public function update(Request $request, $id)
    {
        // Verificar que el usuario autenticado sea administrador
        if (Auth::user()->rol !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $movie = Movie::findOrFail($id);

        $data = $request->validate([
            'titulo'         => 'sometimes|string|max:255',
            'descripcion'    => 'sometimes|string',
            'calificacion'   => 'sometimes|string|max:10',
            'director'       => 'nullable|string|max:255',
            // Se recibe como string y luego se convertirá a array
            'actores'        => 'sometimes|string',
            'duracion'       => 'sometimes|integer|min:1',
            'clasificacion'  => 'sometimes|string|max:10',
            'genero'         => 'sometimes|string|max:100',
            'lenguaje'       => 'sometimes|string|max:100',
            // Ahora se acepta como URL (cadena de texto)
            'imagen'         => 'nullable|string|max:2048',
            'poster'         => 'nullable|string|max:2048',
            'trailer'        => 'sometimes|string|max:255',
            'omdb_id'        => 'sometimes|string|max:20',
        ]);

        // Convertir el campo actores a array, si es que fue enviado como cadena
        if (isset($data['actores'])) {
            $data['actores'] = array_map('trim', explode(',', $data['actores']));
        }

        // Si se sube una imagen (archivo), se procesa la subida
        if ($request->hasFile('imagen')) {
            if ($movie->imagen && Storage::exists(str_replace('/storage', 'public', $movie->imagen))) {
                Storage::delete(str_replace('/storage', 'public', $movie->imagen));
            }
            $imagenPath = $request->file('imagen')->store('public/movies');
            $data['imagen'] = Storage::url($imagenPath);
        } else {
            // Si no se sube archivo y se envía un valor, se usa ese valor (URL)
            if ($request->filled('imagen')) {
                $data['imagen'] = $request->imagen;
            }
        }

        // Se puede aplicar la misma lógica para 'poster' si se quiere soportar ambos casos

        $movie->update($data);

        if ($request->wantsJson()) {
            return response()->json(['movie' => $movie, 'message' => 'Película actualizada con éxito']);
        }
        return redirect()->route('movies.index')->with('success', 'Película actualizada con éxito');
    }


    // Vista de confirmación para eliminar película (Blade)
    public function delete(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        return view('movies.delete', compact('movie'));
    }

    // Eliminar película
    public function destroy(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (Auth::user()->rol !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $movie = Movie::findOrFail($id);

        // Eliminar la imagen si existe
        if ($movie->imagen && Storage::exists(str_replace('/storage', 'public', $movie->imagen))) {
            Storage::delete(str_replace('/storage', 'public', $movie->imagen));
        }

        $movie->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Película eliminada con éxito']);
        }
        return redirect()->route('movies.index')->with('success', 'Película eliminada con éxito');
    }
}
