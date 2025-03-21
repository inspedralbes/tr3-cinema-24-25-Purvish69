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
        
        // Si la petición espera JSON se retorna en ese formato, de lo contrario se muestra la vista
        if (request()->wantsJson()) {
            return response()->json(['sessions' => $sessions]);
        }
        return view('movieSessions.index', compact('sessions'));
    }
    
    // Muestra el formulario para crear una nueva sesión (vía Blade)
    public function create()
    {
        $movies = Movie::all();
        return view('movieSessions.create', compact('movies'));
    }
    
    // Crea una nueva sesión de película
    public function store(Request $request)
    {
        // Solo los administradores pueden crear sesiones
        if (Auth::user()->rol !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        // Validar los datos de entrada
        $data = $request->all();
        $rules = [
            'movie_id'        => 'required|exists:movies,id',
            'fecha'           => 'required|date|after_or_equal:today',
            'hora'            => 'required|in:16:00,18:00,20:00',
            'estado'          => 'required|in:disponible',
            'dia_espectador'  => 'boolean',
            'fila_vip_activa' => 'boolean',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verificar que no exista ya una sesión con la misma fecha y hora
        $existsSameTime = MovieSession::where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->first();
        if ($existsSameTime) {
            $message = 'Ya existe una sesión programada para esta fecha y hora';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return redirect()->back()->with('error', $message)->withInput();
        }

        // Verificar si hay espacio disponible para esa fecha (máximo 3 sesiones por día)
        $sessionsCount = MovieSession::where('fecha', $data['fecha'])->count();
        if ($sessionsCount >= 3) {
            $message = 'Este día ya tiene el máximo de sesiones permitidas (3)';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return redirect()->back()->with('error', $message)->withInput();
        }

        // Crear la sesión
        $session = MovieSession::create([
            'movie_id'        => $data['movie_id'],
            'fecha'           => $data['fecha'],
            'hora'            => $data['hora'],
            'estado'          => 'disponible',
            'dia_espectador'  => isset($data['dia_espectador']) ? $data['dia_espectador'] : false,
            'fila_vip_activa' => isset($data['fila_vip_activa']) ? $data['fila_vip_activa'] : false,
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

        $message = 'Sesión de película creada con éxito';
        if ($request->wantsJson()) {
            return response()->json([
                'session' => $session->load('movie'),
                'message' => $message
            ], 201);
        }
        return redirect()->route('movieSessions.index')->with('success', $message);
    }
    
    // Muestra los detalles de una sesión por su ID
    public function show($id)
    {
        $session = MovieSession::with(['movie', 'seats'])->findOrFail($id);
        if (request()->wantsJson()) {
            return response()->json(['session' => $session]);
        }
        return view('movieSessions.show', compact('session'));
    }
    
    // Muestra el formulario para editar una sesión (vía Blade)
    public function edit($id)
    {
        $session = MovieSession::findOrFail($id);
        $movies = Movie::all();
        return view('movieSessions.edit', compact('session', 'movies'));
    }
    
    // Actualiza una sesión existente
    public function update(Request $request, $id)
    {
        // Solo los administradores pueden actualizar
        if (Auth::user()->rol !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $session = MovieSession::findOrFail($id);

        // No se puede modificar una sesión ya finalizada
        if ($session->estado === 'finalizada') {
            $message = 'No se puede modificar una sesión finalizada';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        // Validar datos a actualizar
        $rules = [
            'estado'          => 'sometimes|in:disponible,cancelada,finalizada',
            'dia_espectador'  => 'sometimes|boolean',
            'fila_vip_activa' => 'sometimes|boolean',
            'movie_id'        => 'sometimes|required|exists:movies,id',
            'fecha'           => 'sometimes|required|date|after_or_equal:today',
            'hora'            => 'sometimes|required|in:16:00,18:00,20:00',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        // Verificar si se está modificando la fecha y hora a una ya existente en otra sesión
        if (isset($data['fecha']) && isset($data['hora'])) {
            $existsSameTime = MovieSession::where('fecha', $data['fecha'])
                ->where('hora', $data['hora'])
                ->where('id', '<>', $session->id)
                ->first();
            if ($existsSameTime) {
                $message = 'Ya existe una sesión programada para esta fecha y hora';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 422);
                }
                return redirect()->back()->with('error', $message)->withInput();
            }
        }

        // Actualizar los campos de la sesión
        $session->update($request->only(['movie_id', 'fecha', 'hora', 'estado', 'dia_espectador', 'fila_vip_activa']));

        // Si se modificó la opción de fila VIP, se actualizan las butacas de la fila F que estén libres
        if ($request->has('fila_vip_activa')) {
            Seat::where('movieSession_id', $session->id)
                ->where('fila', 'F')
                ->where('estado', 'libre')
                ->update([
                    'tipo' => $request->fila_vip_activa ? 'vip' : 'normal'
                ]);
        }

        $message = 'Sesión actualizada con éxito';
        if ($request->wantsJson()) {
            return response()->json([
                'session' => $session->fresh()->load('movie'),
                'message' => $message
            ]);
        }
        return redirect()->route('movieSessions.index')->with('success', $message);
    }
    
    // Elimina una sesión siempre que no tenga entradas vendidas
    public function destroy($id)
    {
        if (Auth::user()->rol !== 'admin') {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return redirect()->back()->with('error', 'Acceso denegado');
        }

        $session = MovieSession::findOrFail($id);
        if ($session->tickets()->count() > 0) {
            $message = 'No se puede eliminar una sesión con entradas vendidas';
            if (request()->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        $session->delete();
        $message = 'Sesión eliminada con éxito';
        if (request()->wantsJson()) {
            return response()->json(['message' => $message]);
        }
        return redirect()->route('movieSessions.index')->with('success', $message);
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
    
    // Verifica disponibilidad para una fecha concreta
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fecha = $request->fecha;

        // Obtener todas las sesiones para esa fecha
        $sessions = MovieSession::where('fecha', $fecha)
            ->with('movie')
            ->get();

        // Contar cuántas sesiones hay por hora
        $horasDisponibles = [
            '16:00' => true,
            '18:00' => true,
            '20:00' => true
        ];

        foreach ($sessions as $session) {
            $horasDisponibles[$session->hora] = false;
        }

        $disponible = in_array(true, $horasDisponibles);

        $horariosOcupados = [];
        foreach ($horasDisponibles as $hora => $disp) {
            if (!$disp) {
                $horariosOcupados[] = $hora;
            }
        }

        return response()->json([
            'fecha'             => $fecha,
            'disponible'        => $disponible,
            'horas_disponibles' => $horasDisponibles,
            'horarios_ocupados' => $horariosOcupados,
            'sessions'          => $sessions,
            'sesiones_totales'  => count($sessions),
            'mensaje'           => $disponible
                ? 'Hay horarios disponibles para crear sesiones en esta fecha'
                : 'No hay horarios disponibles para crear sesiones en esta fecha'
        ]);
    }
    
    // Muestra la vista de confirmación para eliminar una sesión
    public function delete($id)
    {
        $session = MovieSession::findOrFail($id);
        return view('movieSessions.delete', compact('session'));
    }
}
