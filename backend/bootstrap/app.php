<?php

require_once __DIR__ . '/openssl_polyfill.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Desactivar encriptación de cookies si el entorno carece de la extensión nativa de OpenSSL
        $middleware->web(remove: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
        ]);
        $middleware->encryptCookies(except: ['*']);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminCheck::class,
        ]);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && !config('app.debug')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Ha ocurrido un error interno en el servidor.'
                ], 500);
            }
        });
    })->create();
