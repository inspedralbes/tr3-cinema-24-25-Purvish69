@extends('Layout.master')

@section('title', 'Crear Nou Usuari | FilmGalaxy Admin')

@section('styles')
<style>
  .form-card {
    background: #ffffff;
    border: 1px solid #eef0f3;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    max-width: 760px;
    margin: 0 auto;
    overflow: hidden;
  }

  .form-card-header {
    background: linear-gradient(135deg, #22223B 0%, #2b2d42 100%);
    color: #ffffff;
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 2px solid #C8A96E;
  }

  .header-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(200, 169, 110, 0.2);
    color: #C8A96E;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
  }

  .form-card-body {
    padding: 2rem;
  }

  .form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #3b3f5c;
    margin-bottom: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .input-group-text {
    background-color: #f8f9fc;
    border-color: #dde1e6;
    color: #8c93a0;
  }

  .form-control, .form-select {
    border-color: #dde1e6;
    border-radius: 8px;
    padding: 0.65rem 0.9rem;
    font-size: 0.92rem;
    transition: all 0.2s ease;
  }

  .form-control:focus, .form-select:focus {
    border-color: #C8A96E;
    box-shadow: 0 0 0 3px rgba(200, 169, 110, 0.2);
  }

  .btn-gold {
    background: linear-gradient(135deg, #C8A96E 0%, #B89758 100%);
    color: #1b1b2a;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.5rem;
    box-shadow: 0 4px 14px rgba(200, 169, 110, 0.35);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-gold:hover {
    background: linear-gradient(135deg, #d4b882 0%, #c4a160 100%);
    color: #000;
    box-shadow: 0 6px 18px rgba(200, 169, 110, 0.45);
    transform: translateY(-1px);
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Crear Usuari</li>
        </ol>
    </nav>

    <div class="form-card">
        <div class="form-card-header">
            <div class="header-icon-box">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Crear Nou Usuari</h4>
                <p class="mb-0 small text-light opacity-75">Introdueix les dades per donar d'alta un nou compte d'usuari</p>
            </div>
        </div>

        <div class="form-card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
                    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Hi ha errors en el formulari:</h6>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="nombre" class="form-label">Nom <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="Ex: Joan" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="apellido" class="form-label">Cognom <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido') }}" placeholder="Ex: García" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Correu Electrònic <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="usuari@exemple.cat" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="telefono" class="form-label">Telèfon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="Ex: 612345678" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="rol" class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <select name="rol" id="rol" class="form-select">
                                <option value="user" {{ old('rol') === 'user' ? 'selected' : '' }}>Client / Usuari Normal</option>
                                <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label">Contrasenya <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Mínim 6 caràcters" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-color: #dde1e6;">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Cancel·lar
                    </a>
                    <button type="submit" class="btn-gold rounded-pill px-4">
                        <i class="bi bi-check-lg"></i> Crear Usuari
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
});
</script>
@endsection
