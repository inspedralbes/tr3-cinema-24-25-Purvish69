@extends('Layout.master')

@section('title', 'Listado de Tickets')
@section('content')
<div class="container">
  <h1 class="mb-4">Listado de Tickets</h1>
  <div class="mb-3">
    <a href="{{ route('tickets.create') }}" class="btn btn-custom">Crear Nuevo Ticket</a>
  </div>
  
  <!-- Filtro por Película -->
  <form action="{{ route('tickets.filter') }}" method="GET" class="mb-4">
    <div class="row align-items-end">
      <div class="col-md-4">
        <label for="movie_id" class="form-label">Filtrar por Película</label>
        <select name="movie_id" id="movie_id" class="form-control">
          <option value="">-- Selecciona una Película --</option>
          @foreach($movies as $movie)
          <option value="{{ $movie->id }}">{{ $movie->titulo }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-custom">Filtrar</button>
      </div>
    </div>
  </form>

  @if(isset($tickets) && $tickets->count() > 0)
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Película</th>
        <th>Sesión</th>
        <th>Butaca</th>
        <th>Precio</th>
        <th>Código Confirmación</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tickets as $ticket)
      <tr>
        <td>{{ $ticket->id }}</td>
        <td>{{ $ticket->user->name }}</td>
        <td>{{ $ticket->movieSession->movie->titulo }}</td>
        <td>{{ $ticket->movieSession->fecha }} {{ $ticket->movieSession->hora }}</td>
        <td>{{ $ticket->seat->numero ?? 'N/A' }}</td>
        <td>{{ $ticket->precio }}</td>
        <td>{{ $ticket->codigo_confirmacion }}</td>
        <td>
          <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-warning">Editar</a>
          <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este ticket?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <p>No se encontraron tickets.</p>
  @endif
</div>
@endsection
