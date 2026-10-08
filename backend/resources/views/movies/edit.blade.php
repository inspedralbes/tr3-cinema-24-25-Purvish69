@extends('Layout.master')

@section('content')
<div class="container">
    <h1>Editar Película</h1>
    <form action="{{ route('movies.update', $movie->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ $movie->titulo }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required>{{ $movie->descripcion }}</textarea>
        </div>

        <div class="form-group">
            <label for="calificacion">Calificación</label>
            <input type="text" name="calificacion" id="calificacion" class="form-control" value="{{ $movie->calificacion }}">
        </div>

        <div class="form-group">
            <label for="director">Director</label>
            <input type="text" name="director" id="director" class="form-control" value="{{ $movie->director }}">
        </div>

        <div class="form-group">
            <label for="actores">Actores (separados por coma)</label>
            <input type="text" name="actores" id="actores" class="form-control" value="{{ is_array($movie->actores) ? implode(',', $movie->actores) : $movie->actores }}" required>
        </div>

        <div class="form-group">
            <label for="duracion">Duración (minutos)</label>
            <input type="number" name="duracion" id="duracion" class="form-control" value="{{ $movie->duracion }}" required>
        </div>

        <div class="form-group">
            <label for="clasificacion">Clasificación</label>
            <input type="text" name="clasificacion" id="clasificacion" class="form-control" value="{{ $movie->clasificacion }}" required>
        </div>

        <div class="form-group">
            <label for="genero">Género</label>
            <input type="text" name="genero" id="genero" class="form-control" value="{{ $movie->genero }}" required>
        </div>

        <div class="form-group">
            <label for="lenguaje">Lenguaje</label>
            <input type="text" name="lenguaje" id="lenguaje" class="form-control" value="{{ $movie->lenguaje }}" required>
        </div>

        <div class="form-group">
            <label for="imagen">URL de la Imagen</label>
            <input type="url" name="imagen" id="imagen" class="form-control" value="{{ $movie->imagen }}">
        </div>

        <div class="form-group">
            <label for="poster">URL del Poster</label>
            <input type="url" name="poster" id="poster" class="form-control" value="{{ $movie->poster }}">
        </div>

        <div class="form-group">
            <label for="trailer">URL del Trailer</label>
            <input type="url" name="trailer" id="trailer" class="form-control" value="{{ $movie->trailer }}">
        </div>

        <div class="form-group">
            <label for="omdb_id">OMDB ID</label>
            <input type="text" name="omdb_id" id="omdb_id" class="form-control" value="{{ $movie->omdb_id }}">
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Actualizar Película</button>
    </form>
</div>
@endsection
