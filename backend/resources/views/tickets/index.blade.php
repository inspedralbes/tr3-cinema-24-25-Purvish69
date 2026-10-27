@extends('Layout.master')

@section('title', 'Gestió d\'Entrades | FilmGalaxy Admin')

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

  .form-select-custom {
    border-radius: 10px;
    height: 42px;
    border: 1px solid #dde1e6;
    font-size: 0.92rem;
    background-color: #ffffff;
    min-width: 220px;
  }

  .form-select-custom:focus {
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

  .id-pill {
    font-size: 0.75rem;
    font-weight: 600;
    color: #8c93a0;
    background: #f1f3f6;
    padding: 0.15rem 0.45rem;
    border-radius: 6px;
    display: inline-block;
  }

  .seat-pill {
    background: rgba(34, 34, 59, 0.08);
    color: #22223b;
    border: 1px solid rgba(34, 34, 59, 0.15);
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  .code-pill {
    font-family: monospace;
    font-size: 0.78rem;
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
    max-width: 170px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Entrades</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-ticket-perforated text-warning"></i> Gestió d'Entrades
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 0.85rem; font-weight: 700;">{{ count($tickets) }}</span>
            </h1>
            <p class="page-subtitle">Consulta els tiquets venuts, butaques reservades i codis de confirmació.</p>
        </div>
        <div>
            <a href="{{ route('tickets.create') }}" class="btn-gold">
                <i class="bi bi-plus-circle-fill"></i> Crear Nou Ticket
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
        $totalTickets = count($tickets);
        $totalRevenue = $tickets->sum('precio');
        $uniqueFilms = $tickets->map(fn($t) => optional(optional($t->movieSession)->movie)->titulo)->filter()->unique()->count();
    @endphp

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $totalTickets }}</div>
                    <div class="stat-label">Total Entrades Venudes</div>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-ticket-detailed-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-success">{{ number_format($totalRevenue, 2) }} €</div>
                    <div class="stat-label">Recaptació Total</div>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-warning">{{ $uniqueFilms }}</div>
                    <div class="stat-label">Pel·lícules amb Vendes</div>
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
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Cercar per client, pel·lícula, butaca, codi...">
        </div>

        <!-- Filtro por Película Server-Side o Live -->
        <form action="{{ route('tickets.filter') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
            <select name="movie_id" id="movie_id" class="form-select form-select-custom" onchange="this.form.submit()">
                <option value="">-- Totes les Pel·lícules --</option>
                @foreach($movies as $movie)
                    <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>
                        {{ $movie->titulo }}
                    </option>
                @endforeach
            </select>
            @if(request('movie_id'))
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm" title="Netejar filtre">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <span class="text-muted small d-none d-lg-inline" id="ticketsCounter">Mostrant {{ $totalTickets }} entrades</span>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table" id="ticketsTable">
                <thead>
                    <tr>
                        <th style="width: 75px;">ID</th>
                        <th>Client</th>
                        <th>Pel·lícula</th>
                        <th>Sessió</th>
                        <th>Butaca</th>
                        <th>Preu</th>
                        <th>Codi Confirmació</th>
                        <th class="text-end" style="width: 120px;">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $userName = $ticket->user ? ($ticket->user->nombre . ' ' . $ticket->user->apellido) : 'Client General';
                            $userEmail = $ticket->user->email ?? 'Sense email';
                            $movieTitle = optional(optional($ticket->movieSession)->movie)->titulo ?? 'Pel·lícula no disponible';
                            
                            $sessionDate = optional($ticket->movieSession)->fecha 
                                ? (\Carbon\Carbon::parse($ticket->movieSession->fecha)->format('d/m/Y'))
                                : 'N/D';
                            $sessionTime = optional($ticket->movieSession)->hora ?? '';
                            $seatNum = optional($ticket->seat)->numero ?? 'N/A';
                            $code = $ticket->codigo_confirmacion ?? 'N/A';
                            $searchData = strtolower($ticket->id . ' ' . $userName . ' ' . $userEmail . ' ' . $movieTitle . ' ' . $sessionDate . ' ' . $sessionTime . ' ' . $seatNum . ' ' . $code);
                        @endphp
                        <tr class="ticket-row" data-search="{{ $searchData }}">
                            <td>
                                <span class="id-pill">#{{ $ticket->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #22223B; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($userName, 0, 1)) ?: 'U' }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $userName }}</div>
                                        <span class="text-muted small">{{ $userEmail }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $movieTitle }}</div>
                            </td>
                            <td>
                                <span class="text-dark small">
                                    <i class="bi bi-calendar3 text-muted me-1"></i> {{ $sessionDate }}
                                    @if($sessionTime)
                                        <span class="badge bg-light text-dark border ms-1">{{ $sessionTime }}</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="seat-pill">
                                    <i class="bi bi-ticket-perforated"></i> {{ $seatNum }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($ticket->precio, 2) }} €</span>
                            </td>
                            <td>
                                <span class="code-pill" title="{{ $code }}">
                                    {{ $code }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Editar -->
                                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn-action edit" title="Editar entrada">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- Eliminar -->
                                    <button type="button" class="btn-action delete" title="Eliminar entrada"
                                            data-bs-toggle="modal" data-bs-target="#deleteTicketModal"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-ticket-info="Ticket #{{ $ticket->id }} ({{ $movieTitle }} - Butaca {{ $seatNum }})">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="bi bi-ticket-detailed empty-state-icon"></i>
                                <h5 class="fw-bold text-dark">No s'han trobat entrades</h5>
                                <p class="text-muted">No hi ha cap ticket registrat o que coincideixi amb el filtre seleccionat.</p>
                                <a href="{{ route('tickets.create') }}" class="btn-gold mt-2">
                                    <i class="bi bi-plus-circle-fill"></i> Crear Nou Ticket
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="noResults" class="empty-state d-none">
            <i class="bi bi-search empty-state-icon"></i>
            <h5 class="fw-bold text-dark">No s'han trobat entrades</h5>
            <p class="text-muted">Cap entrada coincideix amb la cerca introduïda.</p>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminació --}}
<div class="modal fade" id="deleteTicketModal" tabindex="-1" aria-labelledby="deleteTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="deleteTicketModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminació de Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="bi bi-trash3"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2">Eliminar aquest ticket?</h5>
                <p class="text-muted mb-3">
                    Estàs a punt d'eliminar: <strong id="deleteTicketInfo" class="text-dark">Ticket</strong>. La butaca quedarà alliberada.
                </p>
            </div>
            <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel·lar</button>
                <form id="deleteTicketForm" method="POST" action="">
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
    const rows = document.querySelectorAll('.ticket-row');
    const noResults = document.getElementById('noResults');
    const counter = document.getElementById('ticketsCounter');

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

            if (counter) counter.textContent = `Mostrant ${count} de ${rows.length} entrades`;
            if (noResults) {
                if (count === 0 && rows.length > 0) noResults.classList.remove('d-none');
                else noResults.classList.add('d-none');
            }
        });
    }

    const deleteModal = document.getElementById('deleteTicketModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const ticketId = button.getAttribute('data-ticket-id');
            const ticketInfo = button.getAttribute('data-ticket-info');

            document.getElementById('deleteTicketInfo').textContent = ticketInfo;
            document.getElementById('deleteTicketForm').action = `/tickets/${ticketId}`;
        });
    }
});
</script>
@endsection
