@extends('Layout.master')

@section('title', 'Estadísticas del Cinema')

@section('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #4A4E69, #22223B);
        border-radius: 10px;
        color: #EAE0D5;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 0.5rem 0;
        color: #C8A96E;
    }
    
    .stat-label {
        font-size: 1.2rem;
        color: #f2e9e4;
        margin-bottom: 0;
    }
    
    .stat-icon {
        font-size: 3rem;
        color: #C8A96E;
        opacity: 0.8;
        margin-bottom: 1rem;
    }
    
    .table-custom {
        background-color: #F2E9E4;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table-custom thead {
        background: linear-gradient(135deg, #4A4E69, #22223B);
        color: #EAE0D5;
    }
    
    .table-custom th {
        font-weight: 600;
        padding: 1rem;
    }
    
    .table-custom td {
        padding: 0.8rem 1rem;
        vertical-align: middle;
    }
    
    .dashboard-title {
        color: #22223B;
        margin-bottom: 2rem;
        font-weight: 700;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .dashboard-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 3px;
        background-color: #C8A96E;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h1 class="dashboard-title">Tauler d'Estadístiques</h1>
    
    <div class="row">
        <!-- Tarjeta de Total de Reservas -->
        <div class="col-md-4">
            <div class="stat-card text-center">
                <i class="bi bi-ticket-perforated-fill stat-icon"></i>
                <h2 class="stat-number">{{ $totalReserves }}</h2>
                <p class="stat-label">Total de Reserves</p>
            </div>
        </div>
        
        <!-- Tarjeta de Total de Ingresos -->
        <div class="col-md-4">
            <div class="stat-card text-center">
                <i class="bi bi-cash-coin stat-icon"></i>
                <h2 class="stat-number">{{ number_format($totalIngressos, 2) }}€</h2>
                <p class="stat-label">Ingressos Totals</p>
            </div>
        </div>
        
        <!-- Tarjeta del Total de Sesiones -->
        <div class="col-md-4">
            <div class="stat-card text-center">
                <i class="bi bi-film stat-icon"></i>
                <h2 class="stat-number">{{ count($sessionStats) }}</h2>
                <p class="stat-label">Total de Sessions</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Detall de Totes les Sessions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-custom">
                            <thead>
                                <tr>
                                    <th>Pel·lícula</th>
                                    <th>Sessió</th>
                                    <th>Data i Hora</th>
                                    <th>Estat</th>
                                    <th>Total d'Entrades</th>
                                    <th>Recaptació</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessionStats as $session)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($session->movie->imagen)
                                                <img src="{{ $session->movie->imagen }}" alt="{{ $session->movie->titulo }}" 
                                                     class="img-thumbnail me-2" style="width: 50px; height: auto;">
                                            @endif
                                            <span>{{ $session->movie->titulo }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $session->id }}</td>
                                    <td>{{ $session->fecha->format('d/m/Y') }} {{ $session->hora }}</td>
                                    <td>
                                        <span class="badge {{ $session->estado === 'disponible' ? 'bg-success' : ($session->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary') }} badge-status">
                                            {{ ucfirst($session->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $session->tickets_count ?? 0 }}</td>
                                    <td>{{ number_format($session->tickets_sum_precio ?? 0, 2) }}€</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hi ha sessions disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="4" class="text-end fw-bold">Totals:</td>
                                    <td class="fw-bold">{{ $sessionStats->sum('tickets_count') ?? 0 }}</td>
                                    <td class="fw-bold">{{ number_format($sessionStats->sum('tickets_sum_precio') ?? 0, 2) }}€</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script para cargar los iconos de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        // Añadir enlace a los iconos de Bootstrap si no existen ya
        if (!document.querySelector('link[href*="bootstrap-icons"]')) {
            const iconLink = document.createElement('link');
            iconLink.rel = 'stylesheet';
            iconLink.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css';
            document.head.appendChild(iconLink);
        }
    });
</script>
@endsection
