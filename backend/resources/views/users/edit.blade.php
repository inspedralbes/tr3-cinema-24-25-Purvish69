@extends('Layout.master')

@section('title', 'Editar Usuari: ' . $user->nombre . ' ' . $user->apellido . ' | FilmGalaxy Admin')

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
    justify-content: space-between;
    border-bottom: 2px solid #C8A96E;
  }

  .avatar-preview {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: #ffffff;
    background: {{ $user->rol === 'admin' ? 'linear-gradient(135deg, #a47e34, #d4af37)' : 'linear-gradient(135deg, #4A4E69, #22223B)' }};
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
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
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Editar #{{ $user->id }}</li>
        </ol>
    </nav>

    <div class="form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-preview">
                    {{ strtoupper(substr($user->nombre, 0, 1) . substr($user->apellido, 0, 1)) ?: 'U' }}
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $user->nombre }} {{ $user->apellido }}</h4>
                    <span class="badge bg-light text-dark mt-1" style="font-size: 0.75rem;">ID #{{ $user->id }} &bull; {{ $user->email }}</span>
                </div>
            </div>
            <div>
                @if($user->rol === 'admin')
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold">
                        <i class="bi bi-shield-shaded me-1"></i> Admin
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-2 fw-bold">
                        <i class="bi bi-person me-1"></i> Client
                    </span>
                @endif
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

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="nombre" class="form-label">Nom <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $user->nombre) }}" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="apellido" class="form-label">Cognom <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $user->apellido) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Correu Electrònic <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="telefono" class="form-label">Telèfon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="rol" class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <select name="rol" id="rol" class="form-select">
                                <option value="user" {{ old('rol', $user->rol) === 'user' ? 'selected' : '' }}>Client / Usuari Normal</option>
                                <option value="admin" {{ old('rol', $user->rol) === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label">Nova Contrasenya</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Deixar en blanc per mantenir l'actual">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-color: #dde1e6;">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        <div class="form-text" style="font-size: 0.78rem;">Només omple aquest camp si vols canviar la contrasenya.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Cancel·lar
                    </a>
                    <button type="submit" class="btn-gold rounded-pill px-4">
                        <i class="bi bi-check-lg"></i> Desar Canvis
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
