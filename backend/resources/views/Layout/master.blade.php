<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Panel Administrativo')</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Fondo general con degradado entre dos tonos oscuros */
    body {
      background: linear-gradient(135deg, #22223B, #4A4E69);
      color: #EAE0D5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* Estilos para el navbar */
    .navbar {
      background-color: #1C1C1C;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .navbar-brand {
      color: #C8A96E !important;
      font-weight: bold;
      font-size: 1.5rem;
    }

    .navbar-nav .nav-link {
      color: #EAE0D5 !important;
      transition: color 0.3s;
    }

    .navbar-nav .nav-link:hover {
      color: #C8A96E !important;
    }

    /* Contenedor principal con fondo claro y sutil sombra */
    main {
      background-color: #F2E9E4;
      color: #22223B;
      border-radius: 8px;
      padding: 2rem;
      margin: 2rem auto;
      max-width: 1200px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Estilos personalizados para botones */
    .btn-custom {
      background-color: #C8A96E;
      color: #1C1C1C;
      border: none;
      transition: background-color 0.3s;
    }

    .btn-custom:hover {
      background-color: #9A8C98;
      color: #1C1C1C;
    }

    /* Enlaces generales */
    a {
      color: #C8A96E;
      transition: color 0.3s;
    }

    a:hover {
      color: #9A8C98;
    }

    /* Footer elegante */
    footer {
      background-color: #1C1C1C;
      color: #EAE0D5;
      padding: 1rem 0;
      text-align: center;
      margin-top: 2rem;
    }
  </style>
  @yield('styles')
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">FilmGalaxy</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">Users</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('movies.index') }}">Movies</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('session.create') }}">Crear Session</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          @if(Auth::check())
        <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-link nav-link" type="submit">Logout</button>
        </form>
        </li>
      @else
      <li class="nav-item">
      <a class="nav-link" href="{{ route('login') }}">Login</a>
      </li>
    @endif
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    @yield('content')
  </main>


  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>

</html>