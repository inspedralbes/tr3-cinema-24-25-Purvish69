@extends('Layout.master')

@section('title', 'Confirmar Eliminación de Sesión')

@section('content')
<div class="container">
  <h1 class="mb-4">Confirmar Eliminación de Sesión</h1>

  <div class="alert alert-danger">
    <p>¿Estás seguro de que deseas eliminar la sesión de la película: <strong>{{ $session->movie->titulo ?? 'Sin película' }}</strong> programada para <strong>{{ $session->fecha->format('Y-m-d') }} a las {{ $session->hora }}</strong>?</p>
    <p>Esta acción no se puede deshacer.</p>
  </div>

  <form action="{{ route('movieSessions.destroy', $session->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Eliminar Sesión</button>
    <a href="{{ route('movieSessions.index') }}" class="btn btn-secondary">Volver</a>
  </form>
</div>
@endsection
