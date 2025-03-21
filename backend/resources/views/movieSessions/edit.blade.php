@extends('Layout.master')

@section('title', 'Editar Sesión de Película')

@section('content')
<div class="container">
  <h1 class="mb-4">Editar Sesión de Película</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('movieSessions.update', $session->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="movie_id" class="form-label">Película</label>
      <select name="movie_id" id="movie_id" class="form-select" required>
        <option value="">Seleccione una película</option>
        @foreach ($movies as $movie)
          <option value="{{ $movie->id }}" {{ (old('movie_id', $session->movie_id) == $movie->id) ? 'selected' : '' }}>
            {{ $movie->titulo }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label for="fecha" class="form-label">Fecha</label>
      <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', $session->fecha->format('Y-m-d')) }}" required>
    </div>

    <div class="mb-3">
      <label for="hora" class="form-label">Hora</label>
      <select name="hora" id="hora" class="form-select" required>
        <option value="">Seleccione una hora</option>
        <option value="16:00" {{ old('hora', $session->hora) == '16:00' ? 'selected' : '' }}>16:00</option>
        <option value="18:00" {{ old('hora', $session->hora) == '18:00' ? 'selected' : '' }}>18:00</option>
        <option value="20:00" {{ old('hora', $session->hora) == '20:00' ? 'selected' : '' }}>20:00</option>
      </select>
    </div>

    <!-- Se puede actualizar el estado si se requiere -->
    <div class="mb-3">
      <label for="estado" class="form-label">Estado</label>
      <select name="estado" id="estado" class="form-select" required>
        <option value="disponible" {{ old('estado', $session->estado) == 'disponible' ? 'selected' : '' }}>Disponible</option>
        <option value="cancelada" {{ old('estado', $session->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
        <option value="finalizada" {{ old('estado', $session->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
      </select>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="dia_espectador" id="dia_espectador" value="1" {{ old('dia_espectador', $session->dia_espectador) ? 'checked' : '' }}>
      <label class="form-check-label" for="dia_espectador">Día del espectador</label>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="fila_vip_activa" id="fila_vip_activa" value="1" {{ old('fila_vip_activa', $session->fila_vip_activa) ? 'checked' : '' }}>
      <label class="form-check-label" for="fila_vip_activa">Activar fila VIP</label>
    </div>

    <button type="submit" class="btn btn-custom">Actualizar Sesión</button>
    <a href="{{ route('movieSessions.index') }}" class="btn btn-secondary">Volver</a>
  </form>
</div>
@endsection
