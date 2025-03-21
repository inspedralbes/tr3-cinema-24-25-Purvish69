@extends('Layout.master')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Detalle de la Película</h1>
    <div class="card mb-3">
        <div class="row g-0">
            <!-- Sección de Imagen -->
            <div class="col-md-4">
                @if($movie->imagen)
                <p>
                    <strong>Imagen:</strong><br>
                    <img src="{{ $movie->imagen }}" alt="Imagen de {{ $movie->titulo }}" class="img-fluid" style="max-width:200px;">
                </p>
                @endif
                @if($movie->poster)
                <p>
                    <strong>Poster:</strong><br>
                    <img src="{{ $movie->poster }}" alt="Poster de {{ $movie->titulo }}" class="img-fluid" style="max-width:200px;">
                </p>
                @endif
            </div>
            <!-- Sección de Detalles -->
            <div class="col-md-8">
                <div class="card-body">
                    <h2 class="card-title">{{ $movie->titulo }}</h2>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID:</strong> {{ $movie->id }}</li>
                        <li class="list-group-item"><strong>Descripción:</strong> {{ $movie->descripcion }}</li>
                        <li class="list-group-item"><strong>Calificación:</strong> {{ $movie->calificacion }}</li>
                        <li class="list-group-item"><strong>Director:</strong> {{ $movie->director }}</li>
                        <li class="list-group-item"><strong>Actores:</strong>
                            @if(is_array($movie->actores))
                                {{ implode(', ', $movie->actores) }}
                            @else
                                {{ $movie->actores }}
                            @endif
                        </li>
                        <li class="list-group-item"><strong>Duración:</strong> {{ $movie->duracion }} minutos</li>
                        <li class="list-group-item"><strong>Clasificación:</strong> {{ $movie->clasificacion }}</li>
                        <li class="list-group-item"><strong>Género:</strong> {{ $movie->genero }}</li>
                        <li class="list-group-item"><strong>Lenguaje:</strong> {{ $movie->lenguaje }}</li>
                        <li class="list-group-item"><strong>OMDB ID:</strong> {{ $movie->omdb_id }}</li>
                        @if($movie->trailer)
                            <li class="list-group-item"><strong>Trailer:</strong> <a href="{{ $movie->trailer }}" target="_blank">Ver Trailer</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center my-4">
        <a href="{{ route('movies.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>
</div>
@endsection