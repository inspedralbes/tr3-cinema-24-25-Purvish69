@extends('Layout.master')

@section('title', 'Eliminar Usuari | FilmGalaxy Admin')

@section('styles')
<style>
  .delete-card {
    background: #ffffff;
    border: 1px solid #f9d6d6;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.08);
    max-width: 600px;
    margin: 2rem auto;
    overflow: hidden;
  }

  .delete-card-header {
    background: #dc3545;
    color: #ffffff;
    padding: 1.25rem 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .delete-card-body {
    padding: 2rem;
    text-align: center;
  }

  .warning-icon-box {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin-bottom: 1.25rem;
  }

  .user-preview-box {
    background: #f8f9fa;
    border: 1px solid #eef0f3;
    border-radius: 12px;
    padding: 1.25rem;
    margin: 1.5rem 0;
    text-align: left;
  }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i> Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Usuaris</a></li>
            <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Confirmar Baixa</li>
        </ol>
    </nav>

    <div class="delete-card">
        <div class="delete-card-header">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <h5 class="mb-0 fw-bold">Confirmar Eliminació d'Usuari</h5>
        </div>

        <div class="delete-card-body">
            <div class="warning-icon-box">
                <i class="bi bi-trash3-fill"></i>
            </div>

            <h4 class="fw-bold text-dark mb-2">Estàs segur que vols eliminar aquest usuari?</h4>
            <p class="text-muted mb-0">Aquesta acció és irreversible. L'usuari perdrà tots els seus accessos immediatament.</p>

            <div class="user-preview-box">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 46px; height: 46px; border-radius: 50%; background: #22223B; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        {{ strtoupper(substr($user->nombre, 0, 1) . substr($user->apellido, 0, 1)) ?: 'U' }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $user->nombre }} {{ $user->apellido }}</div>
                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $user->email }} &bull; ID #{{ $user->id }}</div>
                    </div>
                    <div class="ms-auto">
                        @if($user->rol === 'admin')
                            <span class="badge bg-warning text-dark px-2.5 py-1.5"><i class="bi bi-shield-shaded me-1"></i> Admin</span>
                        @else
                            <span class="badge bg-secondary px-2.5 py-1.5"><i class="bi bi-person me-1"></i> Client</span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-flex justify-content-center gap-3">
                @csrf
                @method('DELETE')
                <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Cancel·lar
                </a>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                    <i class="bi bi-trash3-fill me-1"></i> Sí, Confirmar Baixa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
