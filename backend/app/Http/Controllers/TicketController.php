<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MovieSession;
use App\Models\Seat;
use App\Models\Payment;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TicketMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
            // Permitir que seat_id sea un array o un solo valor
            'seat_id'         => 'required',
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

        // Verificar que el pago pertenezca al usuario
        $payment = Payment::findOrFail($request->payment_id);
        if ($payment->user_id != $request->user_id) {
            return response()->json([
                'error' => 'El pago no pertenece al usuario indicado'
            ], 422);
        }

        // Convertir seat_id a array si no lo es
        $seatIds = is_array($request->seat_id) ? $request->seat_id : [$request->seat_id];
        $tickets = [];

        foreach ($seatIds as $seatId) {
            // Verificar que la butaca pertenezca a la sesión y esté libre
            $seat = Seat::findOrFail($seatId);
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

            // Calcular el precio si no se envía
            $precio = $request->has('precio') ? $request->precio : $this->calcularPrecio($request->movieSession_id, $seatId);

            $data = [
                'user_id'            => $request->user_id,
                'movieSession_id'    => $request->movieSession_id,
                'seat_id'            => $seatId,
                'payment_id'         => $request->payment_id,
                'precio'             => $precio,
                'codigo_confirmacion' => Str::uuid()
            ];

            $ticket = Ticket::create($data);
            $seat->update(['estado' => 'ocupada']);
            $tickets[] = $ticket;
        }

        // Enviar correo con el PDF adjunto usando el último ticket creado (la función usa la sesión y el usuario para seleccionar el último ticket)
        $this->sendTicketsByEmail($request->user_id, $request->movieSession_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tickets creados y correo enviado correctamente',
            'data'    => $tickets
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
        // Find the ticket
        $ticket = Ticket::findOrFail($id);
        
        // Update ticket with request data
        $ticket->update([
            'user_id' => $request->user_id,
            'movieSession_id' => $request->movieSession_id,
            'seat_id' => $request->seat_id,
            'payment_id' => $request->payment_id,
            'precio' => $request->precio,
        ]);
        
        // Instead of returning JSON, redirect to index with a success message
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket actualizado exitosamente');
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

        // // Generar el QR code usando  el código de confirmación
        $qrCodePng = QrCode::size(200)->generate($ticket->codigo_confirmacion);
        $ticket->save();
        $ticket->qr_code = base64_encode($qrCodePng);


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
    }

    public function getTicketsByMovie($movieId)
    {
        // Find the movie first to ensure it exists
        $movie = Movie::findOrFail($movieId);

        // Get tickets through movie sessions
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

        // Calculate total tickets and revenue
        $totalTickets = $tickets->count();
        $totalRevenue = $tickets->sum('precio');

        return view('tickets.movie-tickets', [
            'tickets' => $tickets,
            'movie' => $movie,
            'totalTickets' => $totalTickets,
            'totalRevenue' => $totalRevenue
        ]);
    }


    // Para CRUD
    // Obtiene la vista del listado de tickets (con opción de filtrar por película)
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

    // Muestra el formulario para crear un ticket
    public function createView()
    {
        $users    = \App\Models\User::all();
        $sessions = \App\Models\MovieSession::with('movie')->get();
        $seats    = \App\Models\Seat::all();
        $payments = \App\Models\Payment::all();
        return view('tickets.create', compact('users', 'sessions', 'seats', 'payments'));
    }

    // Muestra el formulario para editar un ticket
    public function editView($id)
    {
        $ticket   = Ticket::findOrFail($id);
        $users    = \App\Models\User::all();
        $sessions = \App\Models\MovieSession::with('movie')->get();
        // Si se quiere limitar las butacas a la sesión actual
        $seats    = \App\Models\Seat::where('movieSession_id', $ticket->movieSession_id)->get();
        $payments = \App\Models\Payment::all();
        return view('tickets.edit', compact('ticket', 'users', 'sessions', 'seats', 'payments'));
    }

    // Filtra tickets por película (opción alternativa al index con filtro)
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
