<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;

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


    Route::get('/session/create', [App\Http\Controllers\MovieSessionsController::class, 'create'])->name('session.create');
});
