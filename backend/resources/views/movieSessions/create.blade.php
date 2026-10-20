@extends('Layout.master')

@section('title', 'Crear Sesión de Película')

@section('content')
<div class="container">
  <h1 class="mb-4">Crear Sesión de Película</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('movieSessions.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label for="movie_id" class="form-label">Película</label>
      <select name="movie_id" id="movie_id" class="form-select" required>
        <option value="">Seleccione una película</option>
        @foreach ($movies as $movie)
          <option value="{{ $movie->id }}" {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
            {{ $movie->titulo }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label for="fecha" class="form-label">Fecha (Días actuales o futuros)</label>
      <input type="date" name="fecha" id="fecha" class="form-control" 
             value="{{ old('fecha', date('Y-m-d')) }}" 
             min="{{ date('Y-m-d') }}" required>
      <small class="text-muted">Fecha por defecto establecida a hoy.</small>
    </div>

    <div class="mb-3">
      <label for="hora" class="form-label">Hora</label>
      <select name="hora" id="hora" class="form-select" required>
        <option value="">Seleccione una hora</option>
        <option value="16:00" {{ old('hora') == '16:00' ? 'selected' : '' }}>16:00</option>
        <option value="18:00" {{ old('hora') == '18:00' ? 'selected' : '' }}>18:00</option>
        <option value="20:00" {{ old('hora') == '20:00' ? 'selected' : '' }}>20:00</option>
      </select>
    </div>

    <!-- Campo oculto para estado, ya que se crea como "disponible" -->
    <input type="hidden" name="estado" value="disponible">

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="dia_espectador" id="dia_espectador" value="1" {{ old('dia_espectador') ? 'checked' : '' }}>
      <label class="form-check-label" for="dia_espectador">Día del espectador (Automático los miércoles)</label>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="fila_vip_activa" id="fila_vip_activa" value="1" {{ old('fila_vip_activa') ? 'checked' : '' }}>
      <label class="form-check-label" for="fila_vip_activa">Activar fila VIP (Fila F)</label>
    </div>

    <button type="submit" class="btn btn-custom">Crear Sesión</button>
    <a href="{{ route('movieSessions.index') }}" class="btn btn-secondary">Volver</a>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const fechaInput = document.getElementById('fecha');
    const espectadorCheckbox = document.getElementById('dia_espectador');
    const horaSelect = document.getElementById('hora');
    const vipCheckbox = document.getElementById('fila_vip_activa');

    function checkDiaEspectador() {
      if (fechaInput && fechaInput.value) {
        const [year, month, day] = fechaInput.value.split('-').map(Number);
        const date = new Date(year, month - 1, day);
        if (date.getDay() === 3) {
          espectadorCheckbox.checked = true;
        }
      }
    }

    function checkVipRow() {
      if (horaSelect && horaSelect.value && horaSelect.value !== '16:00') {
        vipCheckbox.checked = true;
      }
    }

    if (fechaInput) {
      fechaInput.addEventListener('change', checkDiaEspectador);
      checkDiaEspectador();
    }
    if (horaSelect) {
      horaSelect.addEventListener('change', checkVipRow);
    }
  });
</script>
@endsection
