<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?: Auth::user();

        if ($user && $user->rol === 'admin') {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Acceso denegado. Se requieren permisos de administrador.'
            ], 403);
        }

        return redirect('/login')->with('error', 'Acceso denegado. Solo administradores.');
    }
}