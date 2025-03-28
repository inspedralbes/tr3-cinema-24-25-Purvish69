<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieSessionsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\TicketController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de usuarios
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::get('/user/tickets', [TicketController::class, 'getUserTickets']);
});

// Rutas de películas
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
});

// Rutas de sesiones de películas de cine
Route::get('/sessions', [MovieSessionsController::class, 'index']);
Route::get('/sessions/{id}', [MovieSessionsController::class, 'show']);
Route::get('/sessions/{id}/seats', [MovieSessionsController::class, 'getSeats']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sessions', [MovieSessionsController::class, 'store']);
    Route::put('/sessions/{id}', [MovieSessionsController::class, 'update']);
    Route::delete('/sessions/{id}', [MovieSessionsController::class, 'destroy']);
    Route::get('movie-sessions/check-availability', [MovieSessionsController::class, 'checkAvailability']);
});
Route::get('/sessions/{sessionId}/tickets', [TicketController::class, 'getSessionTickets']);

// Rutas de asientos
Route::get('/seats', [SeatController::class, 'index']);
Route::get('/seats/{id}', [SeatController::class, 'show']);
Route::post('/seats', [SeatController::class, 'store']);
Route::put('/seats/{id}', [SeatController::class, 'update']);
Route::delete('/seats/{id}', [SeatController::class, 'destroy']);

// Rutas de tickets
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('/tickets/{id}/with-user', [TicketController::class, 'showWithUser']);
Route::get('tickets/precios-sesion/{sessionId}', [TicketController::class, 'getPreciosSesion']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::put('/tickets/{id}', [TicketController::class, 'update']);
Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
Route::get('/tickets/user/{userId}/complete', [TicketController::class, 'getUserTicketsComplete']);
Route::post('/tickets/send-email/{userId}/{sessionId}', [TicketController::class, 'sendTicketsByEmail']);

// Rutas de pagos
Route::get('/payments', [PaymentController::class, 'index']);
Route::get('/payments/{id}', [PaymentController::class, 'show']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::put('/payments/{id}', [PaymentController::class, 'update']);
Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
