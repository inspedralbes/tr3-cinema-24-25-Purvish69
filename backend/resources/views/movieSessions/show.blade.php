@extends('Layout.master')

@section('title', 'Detalle de la Sesión')

@section('content')
<div class="container">
  <h1 class="mb-4">Detalle de la Sesión</h1>

  <div class="card mb-3">
    <div class="card-header">
      Sesión #{{ $session->id }}
    </div>
    <div class="card-body">
      <h5 class="card-title">Película: {{ $session->movie->titulo ?? 'Sin asignar' }}</h5>
      <p class="card-text">
        <strong>Fecha:</strong> {{ $session->fecha->format('Y-m-d') }}<br>
        <strong>Hora:</strong> {{ $session->hora }}<br>
        <strong>Estado:</strong> {{ ucfirst($session->estado) }}<br>
        <strong>Día del espectador:</strong> {{ $session->dia_espectador ? 'Sí' : 'No' }}<br>
        <strong>Fila VIP activada:</strong> {{ $session->fila_vip_activa ? 'Sí' : 'No' }}
      </p>
      <!-- Aquí podrías incluir más detalles o hasta listar los asientos si lo requieres -->
    </div>
  </div>

  <a href="{{ route('movieSessions.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
