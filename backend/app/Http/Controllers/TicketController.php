<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MovieSession;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\Payment;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    // Obtiene los tickets del usuario autenticado
    public function getUserTickets(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $tickets = Ticket::where('user_id', $user->id)
            ->with(['movieSession.movie', 'seat', 'payment'])
            ->latest('created_at')
            ->get();
        return response()->json($tickets);
    }

    // Función para obtener los tickets de una sesión (solo admin o usuarios autenticados para validar disponibilidad)
    public function getSessionTickets(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $tickets = Ticket::where('movieSession_id', $sessionId)
                ->with(['seat'])
                ->get();

            // Si no es admin, no exponer datos del usuario que compró
            if (!$user || $user->rol !== 'admin') {
                $tickets->makeHidden(['user_id', 'payment_id', 'codigo_confirmacion']);
            } else {
                $tickets->load('user');
            }

            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los tickets de la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    // Función para calcular el precio según el tipo de butaca y el campo dia_espectador de la sesión en el SERVIDOR
    private function calcularPrecio($sessionId, $seatId)
    {
        $session = MovieSession::findOrFail($sessionId);
        $seat = Seat::findOrFail($seatId);

        // Precios según si es día del espectador o tarifa estándar
        if ($session->dia_espectador) {
            $precioNormal = 4; // Precio normal en día del espectador
            $precioVIP    = 6; // Precio VIP en día del espectador
        } else {
            $precioNormal = 6; // Precio normal estándar
            $precioVIP    = 8; // Precio VIP estándar
        }

        return ($seat->tipo === 'vip') ? $precioVIP : $precioNormal;
    }

    // Muestra tickets con relaciones y soporte de filtrado
    public function index(Request $request)
    {
        $user = $request->user() ?: auth('sanctum')->user();

        $query = Ticket::with(['movieSession.movie', 'seat', 'payment']);

        // Si se solicita filtrar por usuario
        if ($request->filled('user_id')) {
            if (!$user || ($user->id != $request->user_id && $user->rol !== 'admin')) {
                return response()->json(['message' => 'Acceso no autorizado'], 403);
            }
            $query->where('user_id', $request->user_id);
        } elseif ($user && $user->rol !== 'admin') {
            // Usuario autenticado estándar: mostrar sus propios tickets
            $query->where('user_id', $user->id);
        }

        if ($request->filled('movieSession_id')) {
            $query->where('movieSession_id', $request->movieSession_id);
        }
        if ($request->filled('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        return response()->json($query->latest('created_at')->get());
    }

    // Crea un nuevo ticket dentro de una transacción con lockForUpdate y cálculo de precio en servidor
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movieSession_id' => 'required|exists:movieSessions,id',
            'seat_id'         => 'required',
            'payment_id'      => 'required|exists:payments,id',
            'user_id'         => 'sometimes|exists:users,id',
            'hold_token'      => 'sometimes|nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error de validación al crear el ticket',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Determinar user_id de forma segura desde autenticación
        $authUser = $request->user();
        $userId = $authUser ? $authUser->id : (int) $request->user_id;

        if (!$userId || !User::where('id', $userId)->exists()) {
            return response()->json(['message' => 'Usuario no válido'], 422);
        }

        // Si el usuario autenticado intenta comprar para otro sin ser admin
        if ($authUser && $authUser->id != $userId && $authUser->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado para comprar entradas para otro usuario'], 403);
        }

        // Verificar que el pago pertenezca al usuario y sea válido
        $payment = Payment::findOrFail($request->payment_id);
        if ($payment->user_id != $userId) {
            return response()->json([
                'error' => 'El pago no pertenece al usuario indicado'
            ], 422);
        }

        // Convertir seat_id a array si no lo es (con límite estricto de 10)
        $seatIds = is_array($request->seat_id) ? $request->seat_id : [$request->seat_id];
        if (count($seatIds) > 10) {
            return response()->json(['error' => 'No se pueden comprar más de 10 asientos por transacción'], 422);
        }

        $holdToken = $request->input('hold_token');
        $sessionId = (int) $request->movieSession_id;

        // Ejecutar toda la verificación y creación en transacción con bloqueo
        return DB::transaction(function () use ($seatIds, $sessionId, $holdToken, $userId, $payment, $request) {
            // Bloqueo concurrente de las butacas
            $seats = Seat::whereIn('id', $seatIds)
                ->where('movieSession_id', $sessionId)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($seatIds)) {
                return response()->json([
                    'error' => 'Una o más butacas no pertenecen a la sesión indicada'
                ], 422);
            }

            $conflicts = [];
            $seatsToProcess = [];

            foreach ($seats as $seat) {
                $asientoCode = "{$seat->fila}-{$seat->numero}";

                // 1. Comprobar que no esté ya ocupado
                if ($seat->estado !== 'libre') {
                    $conflicts[] = $asientoCode;
                    continue;
                }

                // 2. Si hay hold_token, comprobar con lockForUpdate que sigue retenido por este token y no expirado
                if ($holdToken) {
                    $isHeld = SeatHold::where('funcion_id', $sessionId)
                        ->where('asiento', $asientoCode)
                        ->where('hold_token', $holdToken)
                        ->where('expires_at', '>', now())
                        ->lockForUpdate()
                        ->exists();

                    if (!$isHeld) {
                        $conflicts[] = $asientoCode;
                        continue;
                    }
                } else {
                    // Si no viene hold_token, verificar que no esté retenido por nadie
                    $isHeldByAnyone = SeatHold::where('funcion_id', $sessionId)
                        ->where('asiento', $asientoCode)
                        ->where('expires_at', '>', now())
                        ->lockForUpdate()
                        ->exists();

                    if ($isHeldByAnyone) {
                        $conflicts[] = $asientoCode;
                        continue;
                    }
                }

                $seatsToProcess[] = [
                    'seat'    => $seat,
                    'asiento' => $asientoCode,
                ];
            }

            // Si alguno falla o está ocupado/expirado, devolver 409
            if (!empty($conflicts)) {
                return response()->json([
                    'message'   => 'Uno o más asientos ya no están disponibles o su reserva temporal ha expirado',
                    'conflicts' => array_values(array_unique($conflicts))
                ], 409);
            }

            $tickets = [];
            $confirmedAsientos = [];

            foreach ($seatsToProcess as $item) {
                $seat = $item['seat'];
                $asientoCode = $item['asiento'];

                // REGLA DE SEGURIDAD: El precio NUNCA se confía del frontend, se calcula estrictamente en el servidor
                $precio = $this->calcularPrecio($sessionId, $seat->id);

                $data = [
                    'user_id'             => $userId,
                    'movieSession_id'     => $sessionId,
                    'seat_id'             => $seat->id,
                    'payment_id'          => $payment->id,
                    'precio'              => $precio,
                    'codigo_confirmacion' => (string) Str::uuid()
                ];

                $ticket = Ticket::create($data);
                $seat->update(['estado' => 'ocupada']);
                $tickets[] = $ticket;
                $confirmedAsientos[] = $asientoCode;
            }

            // Al confirmar la compra, borrar los holds asociados
            SeatHold::where('funcion_id', $sessionId)
                ->whereIn('asiento', $confirmedAsientos)
                ->delete();

            // Enviar correo con el PDF adjunto si es posible
            try {
                $this->sendTicketsByEmail($userId, $sessionId, $payment->id);
            } catch (\Throwable $e) {
                // No revertir la transacción si falla el servicio de correo
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Tickets creados y correo enviado correctamente',
                'data'    => $tickets
            ], 201);
        });
    }

    // Muestra un ticket específico con protección IDOR
    public function show(Request $request, $id)
    {
        $ticket = Ticket::with(['user', 'movieSession.movie', 'seat', 'payment'])->find($id);

        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        $user = $request->user();
        if ($user && $ticket->user_id !== $user->id && $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso no autorizado al ticket'], 403);
        }

        return response()->json($ticket);
    }

    // Actualiza un ticket existente (solo administradores)
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        $ticket = Ticket::findOrFail($id);

        $ticket->update($request->only([
            'user_id',
            'movieSession_id',
            'seat_id',
            'payment_id',
            'precio',
        ]));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'ticket' => $ticket]);
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado exitosamente');
    }

    // Elimina un ticket y libera la butaca asociada (solo administradores)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        $ticket = Ticket::findOrFail($id);

        $seat = Seat::find($ticket->seat_id);
        if ($seat) {
            $seat->update(['estado' => 'libre']);
        }

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
            'dia_espectador' => (bool) $session->dia_espectador,
            'fecha'          => $session->fecha,
            'pelicula'       => $session->movie ? $session->movie->titulo : null
        ]);
    }

    // Obtiene todos los tickets de un usuario con protección contra IDOR
    public function getUserTicketsComplete(Request $request, $userId)
    {
        $user = $request->user();
        if ($user->id != $userId && $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso no autorizado a las entradas del usuario'], 403);
        }

        $tickets = Ticket::where('user_id', $userId)
            ->with(['movieSession.movie', 'seat', 'payment'])
            ->latest('created_at')
            ->get();

        return response()->json($tickets);
    }

    // Muestra un ticket con el usuario asociado con protección IDOR
    public function showWithUser(Request $request, $id)
    {
        $ticket = Ticket::with(['user', 'movieSession.movie', 'seat', 'payment'])->find($id);

        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        $user = $request->user();
        if ($ticket->user_id !== $user->id && $user->rol !== 'admin') {
            return response()->json(['message' => 'Acceso no autorizado al ticket'], 403);
        }

        return response()->json($ticket);
    }

    public function sendTicketsByEmail($userId, $sessionId, $paymentId = null)
    {
        $authUser = request()->user();
        if ($authUser && $authUser->id != $userId && $authUser->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado para enviar tickets de otro usuario'], 403);
        }

        $user = User::findOrFail($userId);
        $query = Ticket::where('user_id', $userId)
            ->where('movieSession_id', $sessionId)
            ->with(['movieSession.movie', 'seat']);

        if ($paymentId) {
            $query->where('payment_id', $paymentId);
        } else {
            $latest = (clone $query)->latest('created_at')->first();
            if ($latest && $latest->payment_id) {
                $query->where('payment_id', $latest->payment_id);
            }
        }

        $tickets = $query->get();

        if ($tickets->isEmpty()) {
            return response()->json([
                'error' => 'No se encontraron tickets para este usuario en la sesión indicada'
            ], 404);
        }

        $session = $tickets->first()->movieSession;
        $sessionInfo = [
            'fecha' => $session ? $session->fecha : null,
            'hora'  => $session ? $session->hora : null,
            'sala'  => $session ? $session->sala : null,
            'movie' => $session ? $session->movie : null
        ];

        try {
            Mail::to($user->email)->send(new TicketMail($user, $tickets, $sessionInfo));
        } catch (\Throwable $e) {
            // Ignorar fallos de envío en desarrollo
        }

        return response()->json([
            'message' => 'Correo enviado correctamente con los tickets comprados'
        ], 200);
    }

    public function getTicketsByMovie($movieId)
    {
        $movie = Movie::findOrFail($movieId);

        $tickets = Ticket::whereHas('movieSession', function ($query) use ($movieId) {
            $query->where('movie_id', $movieId);
        })
            ->with([
                'user',
                'movieSession',
                'movieSession.movie',
                'seat',
                'payment'
            ])
            ->get();

        $totalTickets = $tickets->count();
        $totalRevenue = $tickets->sum('precio');

        return view('tickets.movie-tickets', [
            'tickets'      => $tickets,
            'movie'        => $movie,
            'totalTickets' => $totalTickets,
            'totalRevenue' => $totalRevenue
        ]);
    }

    // Vistas Blade de administración
    public function indexView(Request $request)
    {
        if ($request->has('movie_id') && !empty($request->movie_id)) {
            $tickets = Ticket::whereHas('movieSession', function ($query) use ($request) {
                $query->where('movie_id', $request->movie_id);
            })->with(['user', 'movieSession.movie', 'seat', 'payment'])->get();
        } else {
            $tickets = Ticket::with(['user', 'movieSession.movie', 'seat', 'payment'])->get();
        }
        $movies = \App\Models\Movie::all();
        return view('tickets.index', compact('tickets', 'movies'));
    }

    public function createView()
    {
        $users    = \App\Models\User::all();
        $sessions = \App\Models\MovieSession::with('movie')->get();
        $seats    = \App\Models\Seat::all();
        $payments = \App\Models\Payment::all();
        return view('tickets.create', compact('users', 'sessions', 'seats', 'payments'));
    }

    public function editView($id)
    {
        $ticket   = Ticket::findOrFail($id);
        $users    = \App\Models\User::all();
        $sessions = \App\Models\MovieSession::with('movie')->get();
        $seats    = \App\Models\Seat::where('movieSession_id', $ticket->movieSession_id)->get();
        $payments = \App\Models\Payment::all();
        return view('tickets.edit', compact('ticket', 'users', 'sessions', 'seats', 'payments'));
    }

    public function filterByMovie(Request $request)
    {
        $movieId = $request->movie_id;
        $tickets = Ticket::whereHas('movieSession', function ($query) use ($movieId) {
            $query->where('movie_id', $movieId);
        })->with(['user', 'movieSession.movie', 'seat', 'payment'])->get();
        $movies  = \App\Models\Movie::all();
        return view('tickets.index', compact('tickets', 'movies'));
    }
}
