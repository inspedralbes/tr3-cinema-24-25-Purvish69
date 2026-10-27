<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieSessionsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SeatHoldController;

// Aplicar Rate Limiting general a todas las rutas de la API
Route::middleware(['throttle:api'])->group(function () {

    // 1. AUTENTICACIÓN Y USUARIOS (Rate Limiting estricto: 5 peticiones/min)
    Route::middleware(['throttle:auth'])->group(function () {
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/login', [UserController::class, 'login']);
    });

    // Rutas protegidas de usuario (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/user/{id}', [UserController::class, 'show']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
        Route::post('/logout', [UserController::class, 'logout']);

        // Listado de usuarios: solo accesible por administradores
        Route::get('/users-list', [UserController::class, 'index'])->middleware('admin');

        // Entradas del usuario autenticado y protección IDOR
        Route::get('/user/tickets', [TicketController::class, 'getUserTickets']);
        Route::get('/tickets/user/{userId}/complete', [TicketController::class, 'getUserTicketsComplete']);
        Route::get('/tickets/{id}/with-user', [TicketController::class, 'showWithUser']);
        Route::post('/tickets/send-email/{userId}/{sessionId}', [TicketController::class, 'sendTicketsByEmail']);

        // Pagos del usuario autenticado (protección IDOR)
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
    });

    // 2. PELÍCULAS (Consulta pública, gestión protegida para admin)
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    });

    // 3. SESIONES DE CINE (Consulta pública, gestión protegida para admin)
    Route::get('/sessions', [MovieSessionsController::class, 'index']);
    Route::get('/sessions/{id}', [MovieSessionsController::class, 'show']);
    Route::get('/sessions/{id}/seats', [MovieSessionsController::class, 'getSeats']);
    Route::get('/sessions/{sessionId}/tickets', [TicketController::class, 'getSessionTickets']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/sessions', [MovieSessionsController::class, 'store']);
        Route::post('/sessions/generate', [MovieSessionsController::class, 'generate']);
        Route::put('/sessions/{id}', [MovieSessionsController::class, 'update']);
        Route::delete('/sessions/{id}', [MovieSessionsController::class, 'destroy']);
        Route::get('movie-sessions/check-availability', [MovieSessionsController::class, 'checkAvailability']);
    });

    // 4. BLOQUEOS TEMPORALES DE ASIENTOS (Rate limiting: 20 peticiones/min por IP y por token)
    Route::middleware(['throttle:holds'])->group(function () {
        Route::post('/holds', [SeatHoldController::class, 'store']);
        Route::delete('/holds', [SeatHoldController::class, 'destroy']);
    });
    Route::get('/funciones/{id}/asientos', [SeatHoldController::class, 'getAsientos']);
    Route::get('/sessions/{id}/asientos', [SeatHoldController::class, 'getAsientos']);

    // 5. BUTACAS (Consulta pública, mutaciones solo admin)
    Route::get('/seats', [SeatController::class, 'index']);
    Route::get('/seats/{id}', [SeatController::class, 'show']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/seats', [SeatController::class, 'store']);
        Route::put('/seats/{id}', [SeatController::class, 'update']);
        Route::delete('/seats/{id}', [SeatController::class, 'destroy']);
    });

    // 6. TICKETS Y COMPRA (Rate limiting checkout: 15/min por IP/usuario)
    Route::get('tickets/precios-sesion/{sessionId}', [TicketController::class, 'getPreciosSesion']);

    Route::middleware(['throttle:checkout'])->group(function () {
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::post('/payments', [PaymentController::class, 'store']);
    });

    // Rutas públicas de consulta de tickets
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::put('/tickets/{id}', [TicketController::class, 'update']);
        Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
    });

    // 7. PAGOS (Gestión administrativa)
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::put('/payments/{id}', [PaymentController::class, 'update']);
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    });
});
