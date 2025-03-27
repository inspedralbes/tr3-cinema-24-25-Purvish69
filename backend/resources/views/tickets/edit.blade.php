@extends('Layout.master')

@section('title', 'Editar Ticket')
@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Ticket</h1>
        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario</label>
                <select name="user_id" id="user_id" class="form-control" style="color: black;" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $ticket->user_id == $user->id ? 'selected' : '' }}>
                            ID: {{ $user->id }} - {{  $user->nombre  }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="movieSession_id" class="form-label">Sesión</label>
                <select name="movieSession_id" id="movieSession_id" class="form-control" style="color: black;" required>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $ticket->movieSession_id == $session->id ? 'selected' : '' }}>
                            {{ $session->fecha }} - {{ $session->hora }} - {{ $session->movie->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="seat_id" class="form-label">Butaca</label>
                <select name="seat_id" id="seat_id" class="form-control" style="color: black;" required>
                    @foreach($seats as $seat)
                        <option value="{{ $seat->id }}" {{ $ticket->seat_id == $seat->id ? 'selected' : '' }}>
                            {{ $seat->numero }} ({{ $seat->tipo }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="payment_id" class="form-label">Pago</label>
                <select name="payment_id" id="payment_id" class="form-control" style="color: black;" required>
                    @foreach($payments as $payment)
                        <option value="{{ $payment->id }}" {{ $ticket->payment_id == $payment->id ? 'selected' : '' }}>
                            ID: {{ $payment->id }} - Tarjeta
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="number" step="0.01" name="precio" id="precio" class="form-control"
                        value="{{ $ticket->precio }}">
                </div>
            </div>
            <button type="submit" class="btn btn-custom">Actualizar Ticket</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection