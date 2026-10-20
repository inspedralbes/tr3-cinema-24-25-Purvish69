@extends('Layout.master')

@section('title', 'Listado de Sesiones de Película')

@section('content')
    <div class="container">
        <h1 class="mb-4">Sesiones de Película</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <a href="{{ route('movieSessions.create') }}" class="btn btn-custom">Crear Nueva Sesión</a>

            <form action="{{ route('movieSessions.generate') }}" method="POST" onsubmit="return confirm('¿Deseas generar sesiones automáticas con días actuales (hoy en adelante)?');">
                @csrf
                <button type="submit" class="btn btn-success">
                    Generar Sesiones con Días Actuales
                </button>
            </form>
        </div>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Película</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    <tr>
                        <td>{{ $session->id }}</td>
                        <td>{{ $session->movie->titulo ?? 'Sin Película' }}</td>
                        <td>{{ $session->fecha->format('Y-m-d') }}</td>
                        <td>{{ $session->hora }}</td>
                        <td>{{ ucfirst($session->estado) }}</td>
                        <td>
                            <!-- Botón para ver detalles -->
                            <a href="{{ route('movieSessions.show', $session->id) }}" class="btn btn-info btn-sm">Ver detalles</a>
                            <!-- Botón para editar -->
                            <a href="{{ route('movieSessions.edit', $session->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <!-- Botón para borrar -->
                            <form action="{{ route('movieSessions.destroy', $session->id) }}" method="POST"
                                style="display:inline-block;"
                                onsubmit="return confirm('¿Seguro que deseas eliminar esta sesión?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
