@extends('Layout.master')

@section('title', 'Detalle del Ticket')
@section('content')
<div class="container">
  <h1 class="mb-4">Detalle del Ticket</h1>
  
  @if(isset($ticket))
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Ticket #{{ $ticket->id }}</h5>
      <p><strong>Usuario:</strong> {{ $ticket->user->name }}</p>
      <p><strong>Película:</strong> {{ $ticket->movieSession->movie->titulo }}</p>
      <p><strong>Sesión:</strong> {{ $ticket->movieSession->fecha }} {{ $ticket->movieSession->hora }}</p>
      <p><strong>Butaca:</strong> {{ $ticket->seat->numero ?? 'N/A' }} ({{ $ticket->seat->tipo ?? 'N/A' }})</p>
      <p><strong>Precio:</strong> {{ $ticket->precio }} €</p>
      <p><strong>Código de confirmación:</strong> {{ $ticket->codigo_confirmacion }}</p>
    </div>
    <div class="card-footer">
      <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning">Editar</a>
      <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
  </div>
  @else
  <div class="alert alert-danger">
    El ticket no existe o ha sido eliminado.
  </div>
  <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Volver al listado</a>
  @endif
</div>
@endsection
