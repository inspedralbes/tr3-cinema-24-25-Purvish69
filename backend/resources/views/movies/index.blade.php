@extends('Layout.master')

@section('title', 'Gestió de Pel·lícules | FilmGalaxy Admin')

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

  /* Stat Cards */
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

  .stat-icon.info {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
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

  /* Filter & Search Bar */
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

  /* Table Card */
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

  .movie-thumb {
    width: 48px;
    height: 68px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    background: #22223B;
    flex-shrink: 0;
  }

  .movie-thumb-placeholder {
    width: 48px;
    height: 68px;
    border-radius: 8px;
    background: linear-gradient(135deg, #22223B, #4A4E69);
    color: #C8A96E;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Pel·lícules</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-camera-reels text-warning"></i> Cartellera de Pel·lícules
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 0.85rem; font-weight: 700;">{{ count($movies) }}</span>
            </h1>
            <p class="page-subtitle">Gestiona les pel·lícules, sinopsis, directors i cartells del cinema.</p>
        </div>
        <div>
            <a href="{{ route('movies.create') }}" class="btn-gold">
                <i class="bi bi-film"></i> Nova Pel·lícula
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
        $totalMovies = count($movies);
        $totalSessionsCount = $movies->reduce(function ($carry, $m) {
            return $carry + ($m->sessions ? $m->sessions->count() : 0);
        }, 0);
    @endphp

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $totalMovies }}</div>
                    <div class="stat-label">Total Pel·lícules</div>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-film"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-warning">{{ $totalSessionsCount }}</div>
                    <div class="stat-label">Sessions Programades</div>
                </div>
                <div class="stat-icon gold">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-primary">{{ $movies->whereNotNull('director')->unique('director')->count() }}</div>
                    <div class="stat-label">Directors Registrats</div>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="filter-panel">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Cercar per títol, director, gènere...">
        </div>
        <div>
            <span class="text-muted small" id="moviesCounter">Mostrant {{ $totalMovies }} pel·lícules</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table" id="moviesTable">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Pel·lícula</th>
                        <th>Sinopsi</th>
                        <th>Director</th>
                        <th>Durada</th>
                        <th class="text-end" style="width: 150px;">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movies as $movie)
                        @php
                            $searchData = strtolower($movie->id . ' ' . $movie->titulo . ' ' . $movie->director . ' ' . $movie->genero . ' ' . $movie->descripcion);
                        @endphp
                        <tr class="movie-row" data-search="{{ $searchData }}">
                            <td>
                                <span class="id-pill">#{{ $movie->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if(!empty($movie->imagen))
                                        <img src="{{ $movie->imagen }}" alt="{{ $movie->titulo }}" class="movie-thumb" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="movie-thumb-placeholder" style="display:none;"><i class="bi bi-film"></i></div>
                                    @elseif(!empty($movie->poster))
                                        <img src="{{ $movie->poster }}" alt="{{ $movie->titulo }}" class="movie-thumb" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="movie-thumb-placeholder" style="display:none;"><i class="bi bi-film"></i></div>
                                    @else
                                        <div class="movie-thumb-placeholder"><i class="bi bi-film"></i></div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $movie->titulo }}</div>
                                        @if($movie->genero)
                                            <span class="badge bg-light text-muted border small mt-1">{{ $movie->genero }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 320px;">
                                <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                    {{ $movie->descripcion ?: 'Sense descripció disponible.' }}
                                </p>
                            </td>
                            <td>
                                <span class="text-dark small fw-semibold">
                                    <i class="bi bi-person-video2 text-muted me-1"></i> {{ $movie->director ?: 'No especificat' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 small">
                                    <i class="bi bi-clock me-1 text-warning"></i> {{ $movie->duracion ? $movie->duracion . ' min' : 'N/D' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Ver detalles -->
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn-action view" title="Veure detalls">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Editar -->
                                    <a href="{{ route('movies.edit', $movie->id) }}" class="btn-action edit" title="Editar pel·lícula">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- Eliminar -->
                                    <button type="button" class="btn-action delete" title="Eliminar pel·lícula"
                                            data-bs-toggle="modal" data-bs-target="#deleteMovieModal"
                                            data-movie-id="{{ $movie->id }}"
                                            data-movie-title="{{ $movie->titulo }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-camera-reels empty-state-icon"></i>
                                <h5 class="fw-bold text-dark">No hi ha pel·lícules a la cartellera</h5>
                                <p class="text-muted">Afegeix la primera pel·lícula per començar a programar sessions.</p>
                                <a href="{{ route('movies.create') }}" class="btn-gold mt-2">
                                    <i class="bi bi-film"></i> Crear Pel·lícula
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="noResults" class="empty-state d-none">
            <i class="bi bi-search empty-state-icon"></i>
            <h5 class="fw-bold text-dark">No s'han trobat pel·lícules</h5>
            <p class="text-muted">Cap pel·lícula coincideix amb la cerca introduïda.</p>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminació --}}
<div class="modal fade" id="deleteMovieModal" tabindex="-1" aria-labelledby="deleteMovieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="deleteMovieModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminació
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="bi bi-trash3"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2">Eliminar aquesta pel·lícula?</h5>
                <p class="text-muted mb-3">
                    Estàs a punt d'eliminar la pel·lícula <strong id="deleteMovieTitle" class="text-dark">Pel·lícula</strong>. S'eliminaran també les sessions associades.
                </p>
            </div>
            <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel·lar</button>
                <form id="deleteMovieForm" method="POST" action="">
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
    const rows = document.querySelectorAll('.movie-row');
    const noResults = document.getElementById('noResults');
    const counter = document.getElementById('moviesCounter');

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

            if (counter) counter.textContent = `Mostrant ${count} de ${rows.length} pel·lícules`;
            if (noResults) {
                if (count === 0 && rows.length > 0) noResults.classList.remove('d-none');
                else noResults.classList.add('d-none');
            }
        });
    }

    const deleteModal = document.getElementById('deleteMovieModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const movieId = button.getAttribute('data-movie-id');
            const movieTitle = button.getAttribute('data-movie-title');

            document.getElementById('deleteMovieTitle').textContent = movieTitle;
            document.getElementById('deleteMovieForm').action = `/movies/${movieId}`;
        });
    }
});
</script>
@endsection
