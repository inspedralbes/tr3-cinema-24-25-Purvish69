@extends('Layout.master')

@section('title', 'Tauler d\'Estadístiques | FilmGalaxy Admin')

@section('styles')
<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #e9ecef;
  }

  .page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1b1b2a;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
  }

  .page-subtitle {
    font-size: 0.92rem;
    color: #6c757d;
    margin-top: 0.25rem;
    margin-bottom: 0;
  }

  .stat-card {
    background: #ffffff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    padding: 1.35rem 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  }

  .stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
  }

  .stat-icon.primary {
    background: rgba(34, 34, 59, 0.08);
    color: #22223b;
  }

  .stat-icon.gold {
    background: rgba(200, 169, 110, 0.18);
    color: #a47e34;
  }

  .stat-icon.success {
    background: rgba(25, 135, 84, 0.12);
    color: #198754;
  }

  .stat-icon.info {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
  }

  .stat-value {
    font-size: 1.85rem;
    font-weight: 800;
    color: #1b1b2a;
    line-height: 1.1;
  }

  .stat-label {
    font-size: 0.82rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #838896;
    margin-top: 0.35rem;
  }

  .filter-panel {
    background: #f8f9fc;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
  }

  .search-wrapper {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 420px;
  }

  .search-wrapper .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9499a8;
    pointer-events: none;
  }

  .search-input {
    border-radius: 10px;
    padding-left: 2.5rem;
    padding-right: 1rem;
    height: 42px;
    border: 1px solid #dde1e6;
    font-size: 0.92rem;
    background-color: #ffffff;
    transition: all 0.2s ease;
  }

  .search-input:focus {
    border-color: #C8A96E;
    box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.2);
    outline: none;
  }

  .table-card {
    background: #ffffff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
  }

  .card-top-bar {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #eef0f3;
    background: #f8f9fc;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .custom-table {
    margin-bottom: 0;
    vertical-align: middle;
  }

  .custom-table thead th {
    background-color: #f8f9fc;
    color: #656d78;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eef0f3;
    white-space: nowrap;
  }

  .custom-table tbody tr {
    transition: background-color 0.15s ease;
    border-bottom: 1px solid #f1f3f7;
  }

  .custom-table tbody tr:hover {
    background-color: #fafbfc;
  }

  .custom-table td {
    padding: 1rem 1.25rem;
    color: #2b2d42;
    font-size: 0.92rem;
  }

  .custom-table tfoot td {
    padding: 1.1rem 1.25rem;
    background-color: #f8f9fc;
    border-top: 2px solid #eef0f3;
  }

  .movie-thumb-mini {
    width: 38px;
    height: 52px;
    object-fit: cover;
    border-radius: 6px;
    background: #22223B;
  }

  .movie-thumb-icon {
    width: 38px;
    height: 52px;
    border-radius: 6px;
    background: #22223B;
    color: #C8A96E;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }

  .id-pill {
    font-size: 0.75rem;
    font-weight: 600;
    color: #8c93a0;
    background: #f1f3f6;
    padding: 0.15rem 0.45rem;
    border-radius: 6px;
    display: inline-block;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.7rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
  }

  .status-badge.disponible {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
  }

  .status-badge.cancelada {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
  }

  .status-badge.completada {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
  }

  .empty-state {
    padding: 3.5rem 1.5rem;
    text-align: center;
  }

  .empty-state-icon {
    font-size: 3rem;
    color: #c4c9d4;
    margin-bottom: 1rem;
  }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i> Admin</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Estadístiques</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-graph-up-arrow text-warning"></i> Tauler d'Estadístiques
            </h1>
            <p class="page-subtitle">Rendiment de cartellera, recaptació global i mètriques per sessió.</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir Informe
            </button>
        </div>
    </div>

    @php
        $totalSessionsCount = count($sessionStats);
        $avgRevenue = $totalSessionsCount > 0 ? ($totalIngressos / $totalSessionsCount) : 0;
    @endphp

    {{-- Stat KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $totalReserves }}</div>
                    <div class="stat-label">Total Reserves</div>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-ticket-perforated-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-warning">{{ number_format($totalIngressos, 2) }} €</div>
                    <div class="stat-label">Ingressos Totals</div>
                </div>
                <div class="stat-icon gold">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-success">{{ $totalSessionsCount }}</div>
                    <div class="stat-label">Total Sessions</div>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-primary">{{ number_format($avgRevenue, 2) }} €</div>
                    <div class="stat-label">Mitjana / Sessió</div>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="filter-panel">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Filtrar detall de sessions per pel·lícula, sessió, data...">
        </div>
        <div>
            <span class="text-muted small" id="statsCounter">Mostrant {{ $totalSessionsCount }} sessions</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="card-top-bar">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-list-check me-2 text-warning"></i> Detall de Totes les Sessions
            </h5>
            <span class="badge bg-light text-muted border px-2.5 py-1">Actualitzat en temps real</span>
        </div>

        <div class="table-responsive">
            <table class="table custom-table" id="statsTable">
                <thead>
                    <tr>
                        <th>Pel·lícula</th>
                        <th style="width: 85px;">Sessió</th>
                        <th>Data i Hora</th>
                        <th>Estat</th>
                        <th>Entrades Venudes</th>
                        <th class="text-end">Recaptació</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessionStats as $session)
                        @php
                            $movieTitle = $session->movie->titulo ?? 'Sense Pel·lícula';
                            $status = strtolower($session->estado ?? 'disponible');
                            $formattedDate = $session->fecha ? $session->fecha->format('d/m/Y') : 'N/D';
                            $ticketsCount = $session->tickets_count ?? 0;
                            $revenue = $session->tickets_sum_precio ?? 0;
                            $searchData = strtolower($session->id . ' ' . $movieTitle . ' ' . $formattedDate . ' ' . $session->hora . ' ' . $status);
                        @endphp
                        <tr class="stat-row" data-search="{{ $searchData }}">
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    @if(optional($session->movie)->imagen)
                                        <img src="{{ $session->movie->imagen }}" alt="{{ $movieTitle }}" class="movie-thumb-mini" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <div class="movie-thumb-icon" style="display:none;"><i class="bi bi-film"></i></div>
                                    @else
                                        <div class="movie-thumb-icon"><i class="bi bi-film"></i></div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $movieTitle }}</div>
                                        <span class="text-muted small">ID Pel·lícula: #{{ $session->movie_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="id-pill">#{{ $session->id }}</span>
                            </td>
                            <td>
                                <span class="text-dark small">
                                    <i class="bi bi-calendar3 text-muted me-1"></i> {{ $formattedDate }}
                                    <span class="badge bg-light text-dark border ms-1">{{ $session->hora }} h</span>
                                </span>
                            </td>
                            <td>
                                @if($status === 'disponible')
                                    <span class="status-badge disponible">
                                        <i class="bi bi-check-circle-fill"></i> Disponible
                                    </span>
                                @elseif($status === 'cancelada')
                                    <span class="status-badge cancelada">
                                        <i class="bi bi-x-circle-fill"></i> Cancel·lada
                                    </span>
                                @else
                                    <span class="status-badge completada">
                                        <i class="bi bi-clock-history"></i> {{ ucfirst($session->estado) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold">
                                    <i class="bi bi-ticket-perforated me-1 text-primary"></i> {{ $ticketsCount }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-6">{{ number_format($revenue, 2) }} €</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-bar-chart-line empty-state-icon"></i>
                                <h5 class="fw-bold text-dark">No hi ha dades disponibles</h5>
                                <p class="text-muted">Encara no s'ha generat cap sessió o reserva per analitzar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold text-dark">Totals Globals:</td>
                        <td class="fw-bold text-primary">
                            <i class="bi bi-ticket-perforated me-1"></i> {{ $sessionStats->sum('tickets_count') ?? 0 }} entrades
                        </td>
                        <td class="text-end fw-bold text-success fs-6">
                            {{ number_format($sessionStats->sum('tickets_sum_precio') ?? 0, 2) }} €
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div id="noResults" class="empty-state d-none">
            <i class="bi bi-search empty-state-icon"></i>
            <h5 class="fw-bold text-dark">No s'han trobat sessions</h5>
            <p class="text-muted">Cap sessió coincideix amb el filtre introduït.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.stat-row');
    const noResults = document.getElementById('noResults');
    const counter = document.getElementById('statsCounter');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let count = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (!query || searchData.includes(query)) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (counter) counter.textContent = `Mostrant ${count} de ${rows.length} sessions`;
            if (noResults) {
                if (count === 0 && rows.length > 0) noResults.classList.remove('d-none');
                else noResults.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection
