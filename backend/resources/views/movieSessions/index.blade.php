@extends('Layout.master')

@section('title', 'Listado de Sesiones de Película')

@section('content')
    <div class="container">
        <h1 class="mb-4">Sesiones de Película</h1>

        <a href="{{ route('movieSessions.create') }}" class="btn btn-custom mb-3">Crear Nueva Sesión</a>

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
                            <a href="{{ route('movieSessions.show', $session->id) }}" class="btn btn-info btn-sm">Ver
                                detalles</a>
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