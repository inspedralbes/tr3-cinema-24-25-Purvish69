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
});

// Rutas de películas
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
});

// Rutas de películas de cine - sesiones
Route::get('/sessions', [MovieSessionsController::class, 'index']);
Route::get('/sessions/{id}', [MovieSessionsController::class, 'show']);
Route::get('/sessions/{id}/seats', [MovieSessionsController::class, 'getSeats']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sessions', [MovieSessionsController::class, 'store']);
    Route::put('/sessions/{id}', [MovieSessionsController::class, 'update']);
    Route::delete('/sessions/{id}', [MovieSessionsController::class, 'destroy']);
    Route::get('movie-sessions/check-availability', [MovieSessionsController::class, 'checkAvailability']);
});

// Rutas de tickets
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('tickets/precios-sesion/{sessionId}', [TicketController::class, 'getPreciosSesion']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

    // Rutas de asientos
    Route::get('/seats', [SeatController::class, 'index']);
    Route::get('/seats/{id}', [SeatController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/seats', [SeatController::class, 'store']);
        Route::put('/seats/{id}', [SeatController::class, 'update']);
        Route::delete('/seats/{id}', [SeatController::class, 'destroy']);
    });

    // Rutas de pagos
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::put('/payments/{id}', [PaymentController::class, 'update']);
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    });
});
