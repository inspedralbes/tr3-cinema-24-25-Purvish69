@extends('Layout.master')

@section('content')
<div class="container">
    <h1>Crear Nueva Película</h1>
    <form action="{{ route('movies.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="calificacion">Calificación</label>
            <input type="text" name="calificacion" id="calificacion" class="form-control">
        </div>

        <div class="form-group">
            <label for="director">Director</label>
            <input type="text" name="director" id="director" class="form-control">
        </div>

        <div class="form-group">
            <label for="actores">Actores (separados por coma)</label>
            <input type="text" name="actores" id="actores" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="duracion">Duración (minutos)</label>
            <input type="number" name="duracion" id="duracion" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="clasificacion">Clasificación</label>
            <input type="text" name="clasificacion" id="clasificacion" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="genero">Género</label>
            <input type="text" name="genero" id="genero" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="lenguaje">Lenguaje</label>
            <input type="text" name="lenguaje" id="lenguaje" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="imagen">URL de la Imagen</label>
            <input type="url" name="imagen" id="imagen" class="form-control">
        </div>

        <div class="form-group">
            <label for="poster">URL del Poster</label>
            <input type="url" name="poster" id="poster" class="form-control">
        </div>

        <div class="form-group">
            <label for="trailer">URL del Trailer</label>
            <input type="url" name="trailer" id="trailer" class="form-control">
        </div>

        <div class="form-group">
            <label for="omdb_id">OMDB ID</label>
            <input type="text" name="omdb_id" id="omdb_id" class="form-control">
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Crear Película</button>
    </form>
</div>
@endsection
