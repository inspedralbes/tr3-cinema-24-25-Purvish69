@extends('Layout.master')

@section('title', 'Crear Ticket')
@section('content')
<div class="container">
  <h1 class="mb-4">Crear Ticket</h1>
  <form action="{{ route('tickets.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label for="user_id" class="form-label">Usuario</label>
      <select name="user_id" id="user_id" class="form-control" required>
        <option value="">Selecciona un usuario</option>
        @foreach($users as $user)
        <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="movieSession_id" class="form-label">Sesión</label>
      <select name="movieSession_id" id="movieSession_id" class="form-control" required>
        <option value="">Selecciona una sesión</option>
        @foreach($sessions as $session)
        <option value="{{ $session->id }}">
          {{ $session->fecha }} - {{ $session->hora }} - {{ $session->movie->titulo }}
        </option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="seat_id" class="form-label">Butaca</label>
      <select name="seat_id" id="seat_id" class="form-control" required>
        <option value="">Selecciona una butaca</option>
        @foreach($seats as $seat)
        <option value="{{ $seat->id }}">{{ $seat->numero }} ({{ $seat->tipo }})</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="payment_id" class="form-label">Pago</label>
      <select name="payment_id" id="payment_id" class="form-control" required>
        <option value="">Selecciona un pago</option>
        @foreach($payments as $payment)
        <option value="{{ $payment->id }}">Pago #{{ $payment->id }} - Monto: {{ $payment->monto }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="precio" class="form-label">Precio (opcional)</label>
      <input type="number" name="precio" id="precio" class="form-control" placeholder="Se calculará si se deja vacío">
    </div>
    <button type="submit" class="btn btn-custom">Crear Ticket</button>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection
