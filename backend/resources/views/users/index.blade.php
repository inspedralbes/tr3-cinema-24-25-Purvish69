@extends('Layout.master')

@section('title', 'Gestió d\'Usuaris | FilmGalaxy Admin')

@section('styles')
<style>
  /* Header Styles */
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

  /* Action Buttons */
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

  /* Avatar Circle */
  .avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
    flex-shrink: 0;
    color: #ffffff;
  }

  .avatar-admin {
    background: linear-gradient(135deg, #a47e34, #d4af37);
    box-shadow: 0 2px 8px rgba(164, 126, 52, 0.35);
  }

  .avatar-user {
    background: linear-gradient(135deg, #4A4E69, #22223B);
    box-shadow: 0 2px 8px rgba(34, 34, 59, 0.25);
  }

  /* Badges */
  .role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.3px;
  }

  .role-badge.admin {
    background: rgba(200, 169, 110, 0.18);
    color: #936e26;
    border: 1px solid rgba(200, 169, 110, 0.4);
  }

  .role-badge.user {
    background: rgba(74, 78, 105, 0.12);
    color: #3b3f5c;
    border: 1px solid rgba(74, 78, 105, 0.25);
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

  /* Action Buttons in Row */
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

  /* Empty state */
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Gestió d'Usuaris</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-people-fill text-warning"></i> Gestió d'Usuaris
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 0.85rem; font-weight: 700;">{{ count($users) }}</span>
            </h1>
            <p class="page-subtitle">Administra, cerca i gestiona els comptes d'usuaris del cinema.</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="btn-gold">
                <i class="bi bi-person-plus-fill"></i> Nou Usuari
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
        $totalUsers = count($users);
        $adminCount = $users->where('rol', 'admin')->count();
        $clientCount = $totalUsers - $adminCount;
    @endphp

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Usuaris</div>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-warning">{{ $adminCount }}</div>
                    <div class="stat-label">Administradors</div>
                </div>
                <div class="stat-icon gold">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-value text-primary">{{ $clientCount }}</div>
                    <div class="stat-label">Clients Estàndard</div>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="filter-panel">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Cercar per nom, cognom, email, telèfon...">
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="filter-pills">
                <button type="button" class="filter-pill active" data-filter="all">Tots ({{ $totalUsers }})</button>
                <button type="button" class="filter-pill" data-filter="admin">Admins ({{ $adminCount }})</button>
                <button type="button" class="filter-pill" data-filter="user">Clients ({{ $clientCount }})</button>
            </div>
            <span class="text-muted small d-none d-sm-inline" id="usersCounter">Mostrant {{ $totalUsers }} usuaris</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table" id="usersTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Usuari</th>
                        <th>Contacte</th>
                        <th>Rol</th>
                        <th>Registre</th>
                        <th class="text-end" style="width: 130px;">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $isAdmin = ($user->rol === 'admin');
                            $initials = strtoupper(substr($user->nombre, 0, 1) . substr($user->apellido, 0, 1));
                            $searchData = strtolower($user->id . ' ' . $user->nombre . ' ' . $user->apellido . ' ' . $user->email . ' ' . $user->telefono . ' ' . $user->rol);
                        @endphp
                        <tr class="user-row" data-role="{{ $user->rol ?? 'user' }}" data-search="{{ $searchData }}">
                            <td>
                                <span class="id-pill">#{{ $user->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle {{ $isAdmin ? 'avatar-admin' : 'avatar-user' }}" title="{{ $user->nombre }}">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->nombre }} {{ $user->apellido }}</div>
                                        <div class="text-muted small"><i class="bi bi-person-fill opacity-50"></i> {{ $isAdmin ? 'Administrador del sistema' : 'Client cinema' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark small fw-semibold">
                                        <i class="bi bi-envelope-fill text-muted me-1"></i> {{ $user->email }}
                                    </a>
                                    <span class="text-muted small">
                                        <i class="bi bi-telephone-fill text-muted me-1"></i> {{ $user->telefono ?: 'Sense telèfon' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($isAdmin)
                                    <span class="role-badge admin">
                                        <i class="bi bi-shield-shaded"></i> Admin
                                    </span>
                                @else
                                    <span class="role-badge user">
                                        <i class="bi bi-person-check"></i> Client
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1 opacity-75"></i>
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/D' }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Botó Editar -->
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-action edit" title="Editar usuari">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- Botó Eliminar amb modal -->
                                    <button type="button" class="btn-action delete" title="Eliminar usuari" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                            data-user-id="{{ $user->id }}" 
                                            data-user-name="{{ $user->nombre }} {{ $user->apellido }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-people empty-state-icon"></i>
                                <h5 class="fw-bold text-dark">No hi ha cap usuari registrat</h5>
                                <p class="text-muted">Comença afegint el primer usuari al sistema.</p>
                                <a href="{{ route('users.create') }}" class="btn-gold mt-2">
                                    <i class="bi bi-person-plus-fill"></i> Crear Usuari
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- No results state on search filter --}}
        <div id="noResults" class="empty-state d-none">
            <i class="bi bi-search empty-state-icon"></i>
            <h5 class="fw-bold text-dark">No s'han trobat resultats</h5>
            <p class="text-muted">Cap usuari coincideix amb els criteris de cerca actuals.</p>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="resetSearchBtn">
                Restablir cerca
            </button>
        </div>
    </div>

</div>

{{-- Modal de Confirmació d'Eliminació --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
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
                <h5 class="fw-bold text-dark mb-2">Eliminar aquest usuari?</h5>
                <p class="text-muted mb-3">
                    Estàs a punt d'eliminar a <strong id="deleteUserName" class="text-dark">l'usuari</strong>. Aquesta acció no es pot desfer i s'eliminaran els seus accessos.
                </p>
                <div class="alert alert-warning py-2 px-3 small text-start mb-0">
                    <i class="bi bi-info-circle-fill me-1"></i> Nota: Les entrades i reserves associades també podrien veure's afectades.
                </div>
            </div>
            <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel·lar</button>
                <form id="deleteForm" method="POST" action="">
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
    const rows = document.querySelectorAll('.user-row');
    const noResults = document.getElementById('noResults');
    const counter = document.getElementById('usersCounter');
    const resetSearchBtn = document.getElementById('resetSearchBtn');
    let currentFilter = 'all';

    function filterUsers() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        rows.forEach(row => {
            const role = row.getAttribute('data-role');
            const searchData = row.getAttribute('data-search');

            const matchesFilter = (currentFilter === 'all') || (role === currentFilter);
            const matchesQuery = !query || searchData.includes(query);

            if (matchesFilter && matchesQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (counter) {
            counter.textContent = `Mostrant ${visibleCount} de ${rows.length} usuaris`;
        }

        if (noResults) {
            if (visibleCount === 0 && rows.length > 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', function () {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            filterUsers();
        });
    });

    if (resetSearchBtn) {
        resetSearchBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            currentFilter = 'all';
            filterPills.forEach(p => p.classList.remove('active'));
            if (filterPills[0]) filterPills[0].classList.add('active');
            filterUsers();
        });
    }

    // Modal de confirmació d'eliminació
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            const deleteUserNameElem = document.getElementById('deleteUserName');
            const deleteForm = document.getElementById('deleteForm');

            if (deleteUserNameElem) deleteUserNameElem.textContent = userName;
            if (deleteForm) deleteForm.action = `/users/${userId}`;
        });
    }
});
</script>
@endsection
