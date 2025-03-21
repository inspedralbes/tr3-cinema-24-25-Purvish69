@extends('Layout.master')

@section('content')
<div class="container">
    <h1>Eliminar Película</h1>
    <p>¿Estás seguro de que deseas eliminar la siguiente película?</p>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $movie->titulo }}</h5>
            <p class="card-text">{{ $movie->descripcion }}</p>
        </div>
    </div>

    <form action="{{ route('movies.destroy', $movie->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="{{ route('movies.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
