<?php

use App\Http\Controllers\MovieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [AuthController::class, 'index']);
Route::get('/users/{id}', [AuthController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
});


// API para peliculas
Route::get('/movies', [MovieController::class, 'index']);
Route::post('/movies', [MovieController::class, 'store']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::put('/movies/{id}', [MovieController::class, 'update']);
Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
