@extends('Layout.master')

@section('content')
<style>
    
    /* Fondo y tipografía general */
    body {
        background: linear-gradient(135deg, #22223B 10%, #EAE0D5 100%);
        color: #22223B; /* Azul medianoche */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100%;
    }
    /* Título de la página */
    .page-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
        color: #22223B; /* Azul medianoche */
        font-weight: bold;
    }
    /* Estilo de la tarjeta */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        background: #EAE0D5; /* Marfil ahumado */
    }
    .card-header {
        background: #22223B; /* Azul medianoche */
        color: #F2E9E4; /* Beige claro */
        font-weight: bold;
        font-size: 1.5rem;
        text-align: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    /* Estilo del botón */
    .btn-custom {
        background: #C8A96E; /* Dorado envejecido */
        border: none;
        color: #1C1C1C; /* Negro carbón */
        padding: 10px;
        font-size: 1.1rem;
        border-radius: 5px;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover {
        background: #9A8C98; /* Lavanda grisáceo */
        color: #F2E9E4; /* Beige claro */
    }
    /* Estilo de los campos de formulario */
    .form-control {
        border: 1px solid #4A4E69; /* Gris azulado oscuro */
        border-radius: 5px;
        padding: 10px;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #C8A96E; /* Dorado envejecido */
    }
    /* Estilo de las alertas */
    .alert {
        background: #C9ADA7; /* Rosa viejo */
        color: #1C1C1C; /* Negro carbón */
        border: none;
        border-radius: 5px;
    }
</style>

<div class="container mt-4">
    <div class="page-title">FilmGalaxy</div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Iniciar Sesión</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.process') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
