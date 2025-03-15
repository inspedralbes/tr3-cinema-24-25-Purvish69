@extends('Layout.master')

@section('content')
<div class="container">
    <h1>Listado de Películas</h1>
    <a href="{{ route('movies.create') }}" class="btn btn-primary mb-3">Crear Nueva Película</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Director</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movies as $movie)
            <tr>
                <td>{{ $movie->id }}</td>
                <td>{{ $movie->titulo }}</td>
                <td>{{ $movie->descripcion }}</td>
                <td>{{ $movie->director }}</td>
                <td>
                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-info btn-sm">Ver detalles</a>
                    <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <a href="{{ route('movies.delete', $movie->id) }}" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
