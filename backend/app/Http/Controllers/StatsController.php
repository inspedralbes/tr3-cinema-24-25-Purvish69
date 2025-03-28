<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovieSession;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        // Total de reservas (tickets)
        $totalReserves = Ticket::count();
        
        // Total de ingresos
        $totalIngressos = Ticket::sum('precio');
        
        // Estadísticas detalladas de sesiones con conteos de tickets y sumas de precios
        $sessionStats = MovieSession::with('movie')
            ->withCount('tickets')
            ->withSum('tickets', 'precio')
            ->orderBy('fecha', 'desc')
            ->get();
            
        return view('stats.index', compact('totalReserves', 'totalIngressos', 'sessionStats'));
    }
}
