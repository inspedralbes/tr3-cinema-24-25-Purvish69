<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MovieSession;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        // Obtener el número total de reservas (tickets)
        $totalReserves = Ticket::count();
        
        // Calcular los ingresos totales (suma de los precios de todos los tickets)
        $totalIngressos = Ticket::sum('precio');
        
        // Obtener las sesiones activas con películas en recaudación
        $activeSessions = MovieSession::where('estado', 'activa')
            ->whereDate('fecha', '>=', now()->format('Y-m-d'))
            ->count();
            
        // Obtener estadísticas detalladas por sesión
        $sessionStats = MovieSession::with('movie')
            ->where('estado', 'activa')
            ->whereDate('fecha', '>=', now()->format('Y-m-d'))
            ->withCount('tickets')
            ->withSum('tickets', 'precio')
            ->get();
            
        return view('stats.index', compact(
            'totalReserves', 
            'totalIngressos', 
            'activeSessions',
            'sessionStats'
        ));
    }
}
