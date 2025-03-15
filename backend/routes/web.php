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

    // Rutas para Movies y Session (ajusta según corresponda)
    Route::resource('movies', MovieController::class);
    Route::get('/session/create', function () {
        return view('session.create');
    })->name('session.create');
});