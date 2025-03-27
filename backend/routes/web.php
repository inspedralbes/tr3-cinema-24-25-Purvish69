<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta para mostrar el formulario de login y para procesar el login
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.process');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    // Ruta raíz redirige al listado de usuarios (index.blade.php)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Rutas para el CRUD de usuarios
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{id}/delete', [UserController::class, 'confirmDelete'])->name('users.delete');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Rutas protegidas, Rutas para el CRUD de peliculas

    Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
    Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
    Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
    Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('movies.update');
    Route::get('/movies/{movie}/delete', [MovieController::class, 'delete'])->name('movies.delete');
    Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');


    // Rutas para movieSessions (Sesiones de Películas)
    Route::get('/movieSessions', [App\Http\Controllers\MovieSessionsController::class, 'index'])->name('movieSessions.index');
    Route::get('/movieSessions/create', [App\Http\Controllers\MovieSessionsController::class, 'create'])->name('movieSessions.create');
    Route::post('/movieSessions', [App\Http\Controllers\MovieSessionsController::class, 'store'])->name('movieSessions.store');
    Route::get('/movieSessions/{id}', [App\Http\Controllers\MovieSessionsController::class, 'show'])->name('movieSessions.show');
    Route::get('/movieSessions/{id}/edit', [App\Http\Controllers\MovieSessionsController::class, 'edit'])->name('movieSessions.edit');
    Route::put('/movieSessions/{id}', [App\Http\Controllers\MovieSessionsController::class, 'update'])->name('movieSessions.update');
    Route::get('/movieSessions/{id}/delete', [App\Http\Controllers\MovieSessionsController::class, 'delete'])->name('movieSessions.delete');
    Route::delete('/movieSessions/{id}', [App\Http\Controllers\MovieSessionsController::class, 'destroy'])->name('movieSessions.destroy');

    Route::post('/tickets/send-email/{userId}/{sessionId}', [TicketController::class, 'sendTicketsByEmail']);


Route::middleware(['auth'])->group(function() {
    // Listado y filtro de tickets
    Route::get('/tickets', [TicketController::class, 'indexView'])->name('tickets.index');
    Route::get('/tickets/filter', [TicketController::class, 'filterByMovie'])->name('tickets.filter');

    // Crear ticket
    Route::get('/tickets/create', [TicketController::class, 'createView'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Editar ticket
    Route::get('/tickets/{id}/edit', [TicketController::class, 'editView'])->name('tickets.edit');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');

    // Eliminar ticket
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});

});
