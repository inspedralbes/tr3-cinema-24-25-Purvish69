@extends('Layout.master')

@section('title', 'Sessions de Pel·lícula | FilmGalaxy Admin')

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
    padding: 1.25rem 1.5rem;
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
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
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

  .stat-value {
    font-size: 1.75rem;
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
    margin-top: 0.25rem;
  }

  .btn-gold {
    background: linear-gradient(135deg, #C8A96E 0%, #B89758 100%);
    color: #1b1b2a;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.35rem;
    box-shadow: 0 4px 14px rgba(200, 169, 110, 0.35);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
  }

  .btn-gold:hover {
    background: linear-gradient(135deg, #d4b882 0%, #c4a160 100%);
    color: #000;
    box-shadow: 0 6px 18px rgba(200, 169, 110, 0.45);
    transform: translateY(-1px);
  }

  .btn-emerald {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.35rem;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
  }

  .btn-emerald:hover {
    background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
    transform: translateY(-1px);
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

  .filter-pills {
    display: flex;
    gap: 0.4rem;
    background: #eef1f6;
    padding: 0.25rem;
    border-radius: 10px;
  }

  .filter-pill {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .filter-pill.active {
    background: #ffffff;
    color: #1b1b2a;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  }

  .table-card {
    background: #ffffff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
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

  .movie-thumb-mini {
    width: 36px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    background: #22223B;
  }

  .movie-thumb-icon {
    width: 36px;
    height: 50px;
    border-radius: 6px;
    background: #22223B;
    color: #C8A96E;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
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

  .btn-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.95rem;
  }

  .btn-action.view {
    background-color: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
  }

  .btn-action.view:hover {
    background-color: #16a34a;
    color: #ffffff;
    box-shadow: 0 3px 10px rgba(22, 163, 74, 0.3);
  }

  .btn-action.edit {
    background-color: #f0f4ff;
    color: #3b66d4;
    border-color: #d8e2fd;
  }

  .btn-action.edit:hover {
    background-color: #3b66d4;
    color: #ffffff;
    box-shadow: 0 3px 10px rgba(59, 102, 212, 0.3);
  }

  .btn-action.delete {
    background-color: #fdf2f2;
    color: #d32f2f;
    border-color: #f9d6d6;
  }

  .btn-action.delete:hover {
    background-color: #d32f2f;
    color: #ffffff;
    box-shadow: 0 3px 10px rgba(211, 47, 47, 0.3);
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Sessions</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-calendar2-week text-warning"></i> Sessions de Pel·lícula
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 0.85rem; font-weight: 700;">{{ count($sessions) }}</span>
            </h1>
            <p class="page-subtitle">Programa, consulta i gestiona les sessions i horaris de les projeccions.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('movieSessions.generate') }}" method="POST" onsubmit="return confirm('Vols generar sessions automàtiques a partir d\'avui per a totes les pel·lícules?');">
                @csrf
                <button type="submit" class="btn-emerald">
                    <i class="bi bi-magic"></i> Generar Automàtiques
                </button>
            </form>

            <a href="{{ route('movieSessions.create') }}" class="btn-gold">
                <i class="bi bi-plus-circle-fill"></i> Nova Sessió
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border: 1px solid #c3e6cb;">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border: 1px solid #f5c6cb;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $totalSessions = count($sessions);
        $availableSessions = $sessions->filter(fn($s) => strtolower($s->estado ?? '') === 'disponible')->count();
        $uniqueMovies = $sessions->pluck('movie_id')->unique()->count();
    @endphp

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $totalSessions }}</div>
                    <div class="stat-label">Total Sessions</div>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-success">{{ $availableSessions }}</div>
                    <div class="stat-label">Sessions Disponibles</div>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-warning">{{ $uniqueMovies }}</div>
                    <div class="stat-label">Pel·lícules en Cartellera</div>
                </div>
                <div class="stat-icon gold">
                    <i class="bi bi-film"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="filter-panel">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Cercar per pel·lícula, data (AAAA-MM-DD), hora o estat...">
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="filter-pills">
                <button type="button" class="filter-pill active" data-filter="all">Totes ({{ $totalSessions }})</button>
                <button type="button" class="filter-pill" data-filter="disponible">Disponibles ({{ $availableSessions }})</button>
            </div>
            <span class="text-muted small d-none d-sm-inline" id="sessionsCounter">Mostrant {{ $totalSessions }} sessions</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table" id="sessionsTable">
                <thead>
                    <tr>
                        <th style="width: 75px;">ID</th>
                        <th>Pel·lícula</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Estat</th>
                        <th class="text-end" style="width: 140px;">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        @php
                            $movieTitle = $session->movie->titulo ?? 'Sense Pel·lícula';
                            $status = strtolower($session->estado ?? 'disponible');
                            $formattedDate = $session->fecha ? $session->fecha->format('d/m/Y') : 'N/D';
                            $rawDate = $session->fecha ? $session->fecha->format('Y-m-d') : '';
                            $searchData = strtolower($session->id . ' ' . $movieTitle . ' ' . $rawDate . ' ' . $formattedDate . ' ' . $session->hora . ' ' . $status);
                        @endphp
                        <tr class="session-row" data-status="{{ $status }}" data-search="{{ $searchData }}">
                            <td>
                                <span class="id-pill">#{{ $session->id }}</span>
                            </td>
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
                                <span class="text-dark small fw-semibold">
                                    <i class="bi bi-calendar3 text-muted me-1"></i> {{ $formattedDate }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 small">
                                    <i class="bi bi-clock me-1 text-warning"></i> {{ $session->hora }} h
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
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Ver detalles -->
                                    <a href="{{ route('movieSessions.show', $session->id) }}" class="btn-action view" title="Veure detalls">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Editar -->
                                    <a href="{{ route('movieSessions.edit', $session->id) }}" class="btn-action edit" title="Editar sessió">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- Borrar amb modal -->
                                    <button type="button" class="btn-action delete" title="Eliminar sessió"
                                            data-bs-toggle="modal" data-bs-target="#deleteSessionModal"
                                            data-session-id="{{ $session->id }}"
                                            data-session-title="{{ $movieTitle }} ({{ $formattedDate }} {{ $session->hora }})">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-calendar-x empty-state-icon"></i>
                                <h5 class="fw-bold text-dark">No hi ha cap sessió programada</h5>
                                <p class="text-muted">Pots generar automàticament sessions per als dies actuals o crear-ne una manualment.</p>
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <form action="{{ route('movieSessions.generate') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-emerald">
                                            <i class="bi bi-magic"></i> Generar Automàticament
                                        </button>
                                    </form>
                                    <a href="{{ route('movieSessions.create') }}" class="btn-gold">
                                        <i class="bi bi-plus-circle-fill"></i> Nova Sessió
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="noResults" class="empty-state d-none">
            <i class="bi bi-search empty-state-icon"></i>
            <h5 class="fw-bold text-dark">No s'han trobat sessions</h5>
            <p class="text-muted">Cap sessió coincideix amb la cerca introduïda.</p>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminació --}}
<div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-labelledby="deleteSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="deleteSessionModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Eliminar Sessió
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="bi bi-trash3"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2">Eliminar aquesta sessió de cinema?</h5>
                <p class="text-muted mb-3">
                    Estàs a punt d'eliminar la sessió: <strong id="deleteSessionTitle" class="text-dark">Sessió</strong>. Les butaques i reserves d'aquesta sessió s'eliminaran.
                </p>
            </div>
            <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel·lar</button>
                <form id="deleteSessionForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 rounded-pill fw-bold">
                        <i class="bi bi-trash3-fill me-1"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const filterPills = document.querySelectorAll('.filter-pill');
    const rows = document.querySelectorAll('.session-row');
    const noResults = document.getElementById('noResults');
    const counter = document.getElementById('sessionsCounter');
    let currentFilter = 'all';

    function filterSessions() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let count = 0;

        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            const searchData = row.getAttribute('data-search');

            const matchesFilter = (currentFilter === 'all') || (status === currentFilter);
            const matchesQuery = !query || searchData.includes(query);

            if (matchesFilter && matchesQuery) {
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
    }

    if (searchInput) searchInput.addEventListener('input', filterSessions);

    filterPills.forEach(pill => {
        pill.addEventListener('click', function () {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            filterSessions();
        });
    });

    const deleteModal = document.getElementById('deleteSessionModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const sessionId = button.getAttribute('data-session-id');
            const sessionTitle = button.getAttribute('data-session-title');

            document.getElementById('deleteSessionTitle').textContent = sessionTitle;
            document.getElementById('deleteSessionForm').action = `/movieSessions/${sessionId}`;
        });
    }
});
</script>
@endsection
